// api/update.js
const fs = require('fs');
const path = require('path');

const DB_PATH = path.join(process.cwd(), 'data', 'users.json');

function readDB() {
  try {
    if (!fs.existsSync(DB_PATH)) return { users: [] };
    return JSON.parse(fs.readFileSync(DB_PATH, 'utf8'));
  } catch {
    return { users: [] };
  }
}

function writeDB(data) {
  fs.writeFileSync(DB_PATH, JSON.stringify(data, null, 2));
}

module.exports = async (req, res) => {
  res.setHeader('Access-Control-Allow-Origin', '*');
  res.setHeader('Access-Control-Allow-Methods', 'POST, OPTIONS');
  res.setHeader('Access-Control-Allow-Headers', 'Content-Type');
  
  if (req.method === 'OPTIONS') {
    return res.status(200).end();
  }
  
  if (req.method !== 'POST') {
    return res.json({ success: false, error: 'Method not allowed' });
  }
  
  const { userId, updates } = req.body;
  const db = readDB();
  const userIndex = db.users.findIndex(u => u.id === userId);
  
  if (userIndex === -1) {
    return res.json({ success: false, error: 'User not found' });
  }
  
  // Update user data
  db.users[userIndex] = { ...db.users[userIndex], ...updates };
  writeDB(db);
  
  const { password, ...userWithoutPass } = db.users[userIndex];
  return res.json({ success: true, user: userWithoutPass });
};
