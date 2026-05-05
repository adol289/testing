// api/user/[username].js
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

module.exports = async (req, res) => {
  res.setHeader('Access-Control-Allow-Origin', '*');
  res.setHeader('Access-Control-Allow-Methods', 'GET, OPTIONS');
  
  if (req.method === 'OPTIONS') {
    return res.status(200).end();
  }
  
  const { username } = req.query;
  const db = readDB();
  const user = db.users.find(u => u.username === username);
  
  if (!user) {
    return res.json({ success: false, error: 'User not found' });
  }
  
  const { password, ...userWithoutPass } = user;
  return res.json({ success: true, user: userWithoutPass });
}; 
