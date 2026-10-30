// Convertit design/tokens.json en fichiers prêts pour FigCraft :
//   variables.json          -> run_command variable.import {variables: <chemin>}
//   text-styles.batch.json  -> outil batch {commands: <contenu>} (crée le cadre « Typographie » et un style de texte par token)
// Usage : node tokens-to-figcraft.mjs <design/tokens.json> <dossier de sortie>
import { readFileSync, writeFileSync, mkdirSync } from 'node:fs';
import { join } from 'node:path';

const [tokensPath, outDir] = process.argv.slice(2);
if (!tokensPath || !outDir) {
  console.error('Usage : node tokens-to-figcraft.mjs <tokens.json> <dossier de sortie>');
  process.exit(2);
}
const t = JSON.parse(readFileSync(tokensPath, 'utf8'));
mkdirSync(outDir, { recursive: true });

const hexToRgba = (hex, a = 1) => {
  const n = parseInt(hex.replace('#', ''), 16);
  return { r: ((n >> 16) & 255) / 255, g: ((n >> 8) & 255) / 255, b: (n & 255) / 255, a };
};

const collections = {};
const variables = {};
let vid = 0;
function collection(name, modes) {
  const id = `VariableCollectionId:${name}`;
  collections[id] = {
    id, key: id, name, remote: false, hiddenFromPublishing: false,
    defaultModeId: `${id}/0`,
    modes: modes.map((m, i) => ({ modeId: `${id}/${i}`, name: m })),
    variableIds: []
  };
  return id;
}
function variable(colId, name, type, valuesPerMode) {
  const id = `VariableID:${++vid}`;
  const col = collections[colId];
  variables[id] = {
    id, key: id, name, remote: false, hiddenFromPublishing: false, description: '', codeSyntax: {},
    resolvedType: type, scopes: ['ALL_SCOPES'], variableCollectionId: colId,
    valuesByMode: Object.fromEntries(col.modes.map((m, i) => [m.modeId, valuesPerMode[i] ?? valuesPerMode[0]]))
  };
  col.variableIds.push(id);
}

// Couleurs : deux modes, Clair et Sombre.
const colors = collection('Couleurs', ['Clair', 'Sombre']);
variable(colors, 'color/accent', 'COLOR', [hexToRgba(t.color.accent.value)]);
variable(colors, 'color/onAccent', 'COLOR', [hexToRgba(t.color.onAccent.value)]);
for (const key of Object.keys(t.color.light)) {
  variable(colors, `color/${key}`, 'COLOR', [hexToRgba(t.color.light[key].value), hexToRgba(t.color.dark[key].value)]);
}
for (const [key, v] of Object.entries(t.color.status)) variable(colors, `color/status/${key}`, 'COLOR', [hexToRgba(v.value)]);
for (const [key, v] of Object.entries(t.color.tech)) {
  if (!key.startsWith('$')) variable(colors, `color/tech/${key}`, 'COLOR', [hexToRgba(v)]);
}
variable(colors, 'color/overlay', 'COLOR', [hexToRgba(t.color.overlay.value, t.color.overlay.opacity)]);

// Dimensions : un seul mode.
const dims = collection('Dimensions', ['Valeur']);
for (const [key, v] of Object.entries(t.space)) if (!key.startsWith('$')) variable(dims, `space/${key}`, 'FLOAT', [v]);
for (const [key, v] of Object.entries(t.radius)) variable(dims, `radius/${key}`, 'FLOAT', [v]);
for (const [key, v] of Object.entries(t.size)) variable(dims, `size/${key}`, 'FLOAT', [v]);

writeFileSync(join(outDir, 'variables.json'),
  JSON.stringify({ error: false, status: 200, meta: { variableCollections: collections, variables } }, null, 2));

// Styles de texte : un nœud témoin par style, rangé dans le cadre « Typographie », puis style.create depuis ce nœud.
const weightToStyle = { 400: 'Regular', 500: 'Medium', 600: 'Semi Bold', 700: 'Bold' };
const commands = [{
  command: 'node.create',
  params: { type: 'frame', name: 'Typographie', width: 1440, height: 100,
    props: { layout: 'vertical', gap: 24, padding: 48, sizingV: 'hug', fill: '#FFFFFF' } }
}];
const styleNames = [];
for (const [key, s] of Object.entries(t.type)) {
  if (key.startsWith('$')) continue;
  const family = t.font[s.font].family;
  const variants = [['', s.size, s.lineHeight]];
  if (s.mobile) variants.push(['/mobile', s.mobile.size, s.mobile.lineHeight]);
  for (const [suffix, size, lineHeight] of variants) {
    const name = `${key}${suffix}`;
    const nodeStep = commands.length;
    commands.push({
      command: 'node.create',
      params: {
        type: 'text', name: `Témoin ${name}`, parent: { $step: 0 },
        text: `${name} — ${family} ${weightToStyle[s.weight]} ${size}/${lineHeight}`,
        props: {
          fontFamily: family, fontStyle: weightToStyle[s.weight], fontSize: size,
          lineHeight, letterSpacing: `${+(s.letterSpacing * 100).toFixed(2)}%`
        }
      }
    });
    commands.push({ command: 'style.create', params: { type: 'text', name, from: { $step: nodeStep } } });
    styleNames.push(name);
  }
}
writeFileSync(join(outDir, 'text-styles.batch.json'), JSON.stringify(commands, null, 2));

console.log(`variables.json : ${Object.keys(variables).length} variables dans ${Object.keys(collections).length} collections`);
console.log(`text-styles.batch.json : ${styleNames.length} styles (${styleNames.join(', ')})`);
