// Lecture d'un fichier .figcraft (archive zip contenant document.json), sans dépendance.
import { readFileSync } from 'node:fs';
import { inflateRawSync } from 'node:zlib';

export function readFigcraft(path) {
  const buf = readFileSync(path);
  // Fin du répertoire central : signature 0x06054b50, cherchée depuis la fin.
  let eocd = -1;
  for (let i = buf.length - 22; i >= Math.max(0, buf.length - 65557); i--) {
    if (buf.readUInt32LE(i) === 0x06054b50) { eocd = i; break; }
  }
  if (eocd < 0) throw new Error(`${path} : archive zip invalide`);
  const count = buf.readUInt16LE(eocd + 10);
  let p = buf.readUInt32LE(eocd + 16);
  for (let n = 0; n < count; n++) {
    const method = buf.readUInt16LE(p + 10);
    const compSize = buf.readUInt32LE(p + 20);
    const nameLen = buf.readUInt16LE(p + 28);
    const extraLen = buf.readUInt16LE(p + 30);
    const commentLen = buf.readUInt16LE(p + 32);
    const localOffset = buf.readUInt32LE(p + 42);
    const name = buf.toString('utf8', p + 46, p + 46 + nameLen);
    if (name === 'document.json') {
      const lNameLen = buf.readUInt16LE(localOffset + 26);
      const lExtraLen = buf.readUInt16LE(localOffset + 28);
      const start = localOffset + 30 + lNameLen + lExtraLen;
      const data = buf.subarray(start, start + compSize);
      const raw = method === 0 ? data : inflateRawSync(data);
      return JSON.parse(raw.toString('utf8'));
    }
    p += 46 + nameLen + extraLen + commentLen;
  }
  throw new Error(`${path} : document.json introuvable dans l'archive`);
}

export function rgbaToHex({ r, g, b }) {
  const h = (v) => Math.round(v * 255).toString(16).padStart(2, '0').toUpperCase();
  return `#${h(r)}${h(g)}${h(b)}`;
}
