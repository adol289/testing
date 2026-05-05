// api/users.js
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
  res.setHeader('Access-Control-Allow-Methods', 'GET, POST, DELETE, OPTIONS');
  res.setHeader('Access-Control-Allow-Headers', 'Content-Type');
  
  if (req.method === 'OPTIONS') {
    return res.status(200).end();
  }
  
  const db = readDB();
  
  // GET all users
  if (req.method === 'GET') {
    const usersWithoutPass = db.users.map(({ password, ...user }) => user);
    return res.json({ success: true, users: usersWithoutPass });
  }
  
  // DELETE user (admin only)
  if (req.method === 'DELETE') {
    const { username, adminKey } = req.body;
    if (adminKey !== 'PUTRA_ADMIN_SECRET_2026') {
      return res.json({ success: false, error: 'Unauthorized' });
    }
    
    const userIndex = db.users.findIndex(u => u.username === username);
    if (userIndex === -1) {
      return res.json({ success: false, error: 'User not found' });
    }
    
    db.users.splice(userIndex, 1);
    writeDB(db);
    return res.json({ success: true, message: 'User deleted' });
  }
  
  return res.json({ success: false, error: 'Method not allowed' });
};
