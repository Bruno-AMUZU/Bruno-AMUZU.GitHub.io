// Vérifie une maquette FigCraft (.figcraft) contre la spec design/.
// Usage : node verify.mjs <maquette.figcraft> <dossier design> [--page "<Nom>"] [--components] [--out rapport.md]
//   --page        ne vérifie que les cadres de cette page, et exige tous ses cadres attendus
//   --components  vérifie la page Composants (composants, styles de texte, variables)
// Code de sortie : 0 = aucune erreur, 1 = au moins une erreur, 2 = mauvaise utilisation.
import { readFileSync, writeFileSync } from 'node:fs';
import { join } from 'node:path';
import { readFigcraft, rgbaToHex } from './figcraft-file.mjs';

const args = process.argv.slice(2);
const opt = (name) => { const i = args.indexOf(name); return i >= 0 ? args[i + 1] : undefined; };
const [filePath, designDir] = args.filter((a, i) => !a.startsWith('--') && !['--page', '--out'].includes(args[i - 1]));
if (!filePath || !designDir) {
  console.error('Usage : node verify.mjs <maquette.figcraft> <dossier design> [--page "<Nom>"] [--components] [--out rapport.md]');
  process.exit(2);
}
const onlyPage = opt('--page');
const checkComponents = args.includes('--components');
const outPath = opt('--out');

const doc = readFigcraft(filePath);
const tokens = JSON.parse(readFileSync(join(designDir, 'tokens.json'), 'utf8'));
const structure = JSON.parse(readFileSync(join(designDir, 'structure.json'), 'utf8'));
const content = JSON.parse(readFileSync(join(designDir, `content.${structure.conventions.lang}.json`), 'utf8'));
const conv = structure.conventions;

const errors = [];
const warnings = [];
const info = [];
const err = (where, msg) => errors.push(`**${where}** — ${msg}`);
const warn = (where, msg) => warnings.push(`**${where}** — ${msg}`);

// ---------- Valeurs autorisées issues des tokens ----------
const allowedColors = new Set();
const addColor = (hex) => allowedColors.add(hex.toUpperCase());
addColor(tokens.color.accent.value);
addColor(tokens.color.onAccent.value);
for (const theme of ['light', 'dark']) for (const v of Object.values(tokens.color[theme])) addColor(v.value);
for (const v of Object.values(tokens.color.status)) addColor(v.value);
for (const [k, v] of Object.entries(tokens.color.tech)) if (!k.startsWith('$')) addColor(v);
addColor(tokens.color.overlay.value);

const allowedSpace = new Set(Object.entries(tokens.space).filter(([k]) => !k.startsWith('$')).map(([, v]) => v));
const allowedRadius = new Set([0, ...Object.values(tokens.radius)]);
const fontFamilies = new Set(Object.values(tokens.font).map((f) => f.family));
const expectedStyles = [];
for (const [k, s] of Object.entries(tokens.type)) {
  if (k.startsWith('$')) continue;
  expectedStyles.push(k);
  if (s.mobile) expectedStyles.push(`${k}/mobile`);
}

// ---------- Textes autorisés issus du contenu ----------
const contentStrings = new Set();
(function collect(v) {
  if (typeof v === 'string') { contentStrings.add(v.trim()); return; }
  if (Array.isArray(v)) return v.forEach(collect);
  if (v && typeof v === 'object') Object.values(v).forEach(collect);
})(content);
const isAllowedText = (text) => {
  const s = text.trim();
  if (!s || contentStrings.has(s)) return true;
  if (/^\[[^\]]*\]$/.test(s)) return true;                 // espace réservé [À FOURNIR], [N], [Visuel]…
  if (/^[\d\s→←↑·⋮+%/:.-]+$/.test(s)) return true;          // chiffres, flèches, séparateurs
  const parts = s.split(/\s*(?:·|\n)\s*/).filter(Boolean);
  return parts.length > 1 && parts.every((p) => contentStrings.has(p) || /^\[[^\]]*\]$/.test(p) || /^[\d\s→←]+$/.test(p));
};

// ---------- Index du document ----------
const nodes = doc.nodes;
const byId = (id) => nodes[String(id)];
const styleById = Object.fromEntries((doc.styles || []).map((s) => [s.id, s]));
const collections = doc.variables?.collections || [];
const colorCollection = collections.find((c) => c.name === conv.colorCollection);

function* walk(id, insideInstance = false) {
  const n = byId(id);
  if (!n) return;
  yield { n, insideInstance };
  const inInst = insideInstance || n.type === 'instance';
  for (const c of n.children || []) yield* walk(c, inInst);
}
const pathOf = (n) => {
  const parts = [];
  for (let cur = n; cur && cur.type !== 'page'; cur = byId(cur.parent)) parts.unshift(cur.name);
  return parts.join(' › ');
};

// ---------- Contrôles par calque ----------
function checkNode(n, insideInstance) {
  const where = pathOf(n);
  const bound = n.bound || {};
  const boundFill = Object.keys(bound).some((k) => k.startsWith('fill'));
  const boundStroke = Object.keys(bound).some((k) => k.startsWith('stroke'));
  const fillKind = n.type === 'text' ? 'texte' : 'fond';
  for (const [kind, paints, isBound] of [[fillKind, n.fills, boundFill], ['contour', n.strokes, boundStroke]]) {
    for (const p of paints || []) {
      if (p.visible === false || p.type !== 'solid') continue;
      const hex = rgbaToHex(p.color);
      if (!allowedColors.has(hex)) err(where, `couleur de ${kind} ${hex} absente des tokens`);
      else if (!isBound && !insideInstance) warn(where, `couleur de ${kind} ${hex} saisie à la main (non liée à une variable)`);
    }
  }
  if (n.type === 'text') {
    const fam = n.style?.font_family;
    if (fam && !fontFamilies.has(fam)) err(where, `police « ${fam} » hors spec`);
    const sid = n.style?.style_id || n.styles?.text;
    if (!sid && !n.source) err(where, 'texte sans style de texte');
    else if (sid && !styleById[sid]) err(where, `style de texte ${sid} inconnu`);
    else if (sid && !expectedStyles.includes(styleById[sid].name)) err(where, `style « ${styleById[sid].name} » hors tokens`);
  }
  const L = n.layout;
  if (L && L.mode && L.mode !== 'none') {
    if (!allowedSpace.has(L.gap)) err(where, `gap ${L.gap} hors échelle d'espacement`);
    if (L.wrap && !allowedSpace.has(L.cross_gap)) err(where, `gap de retour à la ligne ${L.cross_gap} hors échelle`);
    for (const v of L.padding || []) if (!allowedSpace.has(v)) { err(where, `padding ${v} hors échelle`); break; }
  }
  for (const r of n.corners?.radii || []) if (!allowedRadius.has(r)) { err(where, `arrondi ${r} hors tokens`); break; }
}

// ---------- Cadres attendus ----------
function expectedFrames(pageName) {
  const p = structure.pages[pageName];
  const list = [];
  const themes = p.themes || structure.themes;
  for (const f of Object.keys(structure.formats)) for (const th of themes) list.push(`${pageName} / ${f} / ${th}`);
  for (const [state, s] of Object.entries(p.states || {})) {
    for (const f of s.formats || Object.keys(structure.formats)) for (const th of s.themes || ['Clair']) list.push(`${pageName} (${state}) / ${f} / ${th}`);
  }
  return list;
}
const frameRe = /^(.+?)(?: \((.+)\))? \/ (\w+) \/ (\w+)$/;

// ---------- Parcours des pages ----------
const pageNodes = doc.pages.map(byId);
const componentsPage = pageNodes.find((p) => p.name === conv.componentsPage);
const seenFrames = new Set();
let checkedFrames = 0;

for (const page of pageNodes) {
  const isComponents = page === componentsPage;
  if (isComponents && !checkComponents) continue;
  if (!isComponents && checkComponents && !onlyPage) continue;
  for (const topId of page.children || []) {
    const top = byId(topId);
    if (isComponents) {
      for (const { n, insideInstance } of walk(topId)) checkNode(n, insideInstance);
      continue;
    }
    if (top.type !== 'frame') { warn(`${page.name} › ${top.name}`, 'calque hors d\'un cadre de page'); continue; }
    const m = frameRe.exec(top.name);
    if (!m || !structure.pages[m[1]]) {
      (onlyPage ? warn : err)(top.name, 'nom de cadre hors convention « {page} / {format} / {thème} » ou page inconnue');
      continue;
    }
    const [, pageName, state, format, theme] = m;
    if (onlyPage && pageName !== onlyPage) continue;
    seenFrames.add(top.name);
    checkedFrames++;
    const spec = structure.pages[pageName];
    const stateSpec = state ? spec.states?.[state] : null;
    if (state && !stateSpec) { err(top.name, `état « ${state} » absent de structure.json`); continue; }
    if (!structure.formats[format]) err(top.name, `format « ${format} » inconnu`);
    else if (top.width !== structure.formats[format].width) err(top.name, `largeur ${top.width} au lieu de ${structure.formats[format].width}`);
    if (!structure.themes.includes(theme)) err(top.name, `thème « ${theme} » inconnu`);
    else if (colorCollection) {
      const modeId = top.var_modes?.[colorCollection.id];
      const modeName = modeId ? colorCollection.modes.find((mo) => mo.id === modeId)?.name : colorCollection.modes[0]?.name;
      if (modeName !== theme) err(top.name, `mode de couleur « ${modeName} » au lieu de « ${theme} » (variable.setMode sur le cadre)`);
    }

    // Sections dans l'ordre du document
    const sections = [];
    const looseShapes = [];
    const texts = [];
    (function scan(id, insideInstance) {
      const n = byId(id);
      if (!n) return;
      checkNode(n, insideInstance);
      if (n.name?.startsWith(conv.sectionPrefix)) sections.push(n.name.slice(conv.sectionPrefix.length).trim());
      if (n.type === 'text') texts.push(n);
      if (!insideInstance && n !== top && ['text', 'rectangle', 'ellipse', 'vector', 'line', 'polygon', 'star'].includes(n.type)) looseShapes.push(n.name);
      if (!insideInstance && n.type === 'frame' && (n.children || []).length > 1 && !(n.layout && n.layout.mode && n.layout.mode !== 'none')) {
        warn(pathOf(n), 'conteneur sans auto layout');
      }
      for (const c of n.children || []) scan(c, insideInstance || n.type === 'instance');
    })(topId, false);

    const expected = stateSpec?.sections || spec.sectionsByFormat?.[format] || spec.sections;
    if (JSON.stringify(sections) !== JSON.stringify(expected)) {
      const missing = expected.filter((s) => !sections.includes(s));
      const extra = sections.filter((s) => !expected.includes(s));
      let msg = `sections [${sections.join(', ')}] ≠ attendu [${expected.join(', ')}]`;
      if (missing.length) msg += ` — manquantes : ${missing.join(', ')}`;
      if (extra.length) msg += ` — en trop : ${extra.join(', ')}`;
      err(top.name, msg);
    }
    if (looseShapes.length) warn(top.name, `${looseShapes.length} calque(s) hors composant : ${looseShapes.slice(0, 8).join(', ')}${looseShapes.length > 8 ? '…' : ''}`);

    // Contenu
    const forbiddenWords = spec.forbiddenText || [];
    for (const t of texts) {
      const s = t.characters || '';
      if (!isAllowedText(s)) warn(pathOf(t), `texte absent de content.${conv.lang}.json : « ${s.slice(0, 60)} »`);
      for (const f of structure.forbiddenEverywhere) if (new RegExp(f.pattern, 'i').test(s)) err(pathOf(t), `${f.reason} : « ${s.slice(0, 60)} »`);
      for (const w of forbiddenWords) {
        const re = new RegExp(`(^|[^\\p{L}])${w.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')}($|[^\\p{L}])`, 'u');
        if (re.test(s)) err(pathOf(t), `« ${w} » interdit sur cette page`);
      }
    }
  }
}

// ---------- Cadres manquants ----------
if (onlyPage) {
  if (!structure.pages[onlyPage]) { console.error(`Page « ${onlyPage} » absente de structure.json`); process.exit(2); }
  for (const f of expectedFrames(onlyPage)) if (!seenFrames.has(f)) err(f, 'cadre attendu manquant');
} else if (!checkComponents) {
  for (const p of Object.keys(structure.pages)) {
    const missing = expectedFrames(p).filter((f) => !seenFrames.has(f));
    if (missing.length) info.push(`${p} : ${missing.length} cadre(s) pas encore produit(s)`);
  }
}

// ---------- Composants, styles, variables ----------
if (checkComponents) {
  if (!componentsPage) err('Document', `page « ${conv.componentsPage} » absente`);
  const compNames = Object.values(nodes)
    .filter((n) => /^component/.test(n.type) || n.type === 'component_set' || n.type === 'componentSet')
    .map((n) => n.name);
  for (const c of structure.components) {
    if (!compNames.some((name) => name === c || name.startsWith(`${c}/`) || name.startsWith(`${c} `))) err('Composants', `composant « ${c} » manquant`);
  }
  const styleNames = (doc.styles || []).filter((s) => s.type === 'text').map((s) => s.name);
  for (const s of expectedStyles) if (!styleNames.includes(s)) err('Styles', `style de texte « ${s} » manquant`);
  if (!colorCollection) err('Variables', `collection « ${conv.colorCollection} » absente`);
  else {
    const modes = colorCollection.modes.map((m) => m.name);
    for (const th of structure.themes) if (!modes.includes(th)) err('Variables', `mode « ${th} » absent de la collection ${conv.colorCollection}`);
  }
}

// ---------- Rapport ----------
const scope = onlyPage ? `page « ${onlyPage} »` : checkComponents ? 'composants' : 'document entier';
const lines = [
  `# Vérification de la maquette — ${scope}`,
  '',
  `Fichier : \`${filePath}\` · cadres vérifiés : ${checkedFrames}`,
  '',
  `**Résultat : ${errors.length ? `❌ ${errors.length} erreur(s)` : '✅ aucune erreur'} · ${warnings.length} avertissement(s)**`,
  ''
];
if (errors.length) lines.push('## Erreurs (à corriger)', '', ...errors.map((e) => `- ${e}`), '');
if (warnings.length) lines.push('## Avertissements (à examiner)', '', ...warnings.map((w) => `- ${w}`), '');
if (info.length) lines.push('## Avancement', '', ...info.map((i) => `- ${i}`), '');
const report = lines.join('\n');
if (outPath) writeFileSync(outPath, report);
console.log(report);
process.exit(errors.length ? 1 : 0);
