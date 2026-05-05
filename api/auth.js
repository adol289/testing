// api/auth.js
const fs = require('fs');
const path = require('path');

const DB_PATH = path.join(process.cwd(), 'data', 'users.json');

// Helper baca database
function readDB() {
  try {
    if (!fs.existsSync(DB_PATH)) {
      const initialData = {
        users: [{
          id: 'admin_1',
          username: 'admin',
          password: 'admin123',
          isAdmin: true,
          displayName: '@Xtxz Putra',
          bio: '🚀 Creator | Developer | Gamer,⚡ Roblox Exploit & Web Dev,🎮 Live everyday!',
          avatar: null,
          links: {
            instagram: 'https://instagram.com/xtxzputra',
            tiktok: 'https://tiktok.com/@xtxzputra',
            youtube: 'https://youtube.com/c/xtxzputra',
            github: 'https://github.com/xtxzputra',
            whatsapp: 'https://wa.me/6281234567890',
            discord: 'https://discord.gg/invite/xtxzputra'
          },
          backTexts: {
            instagram: '@xtxzputra',
            tiktok: '@xtxzputra',
            youtube: 'Xtxz Putra',
            github: 'github/xtxzputra'
          },
          customLinks: [],
          spotifyId: '3LR1I9C0C1Jzl6wJuS9int',
          theme: 'light',
          bgImage: '',
          createdAt: new Date().toISOString()
        }]
      };
      fs.writeFileSync(DB_PATH, JSON.stringify(initialData, null, 2));
      return initialData;
    }
    return JSON.parse(fs.readFileSync(DB_PATH, 'utf8'));
  } catch (err) {
    return { users: [] };
  }
}

// Helper simpan database
function writeDB(data) {
  fs.writeFileSync(DB_PATH, JSON.stringify(data, null, 2));
}

module.exports = async (req, res) => {
  // CORS headers
  res.setHeader('Access-Control-Allow-Origin', '*');
  res.setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
  res.setHeader('Access-Control-Allow-Headers', 'Content-Type');
  
  if (req.method === 'OPTIONS') {
    return res.status(200).end();
  }
  
  if (req.method !== 'POST') {
    return res.status(405).json({ error: 'Method not allowed' });
  }
  
  const { action, username, password, userData } = req.body;
  
  // LOGIN
  if (action === 'login') {
    const db = readDB();
    const user = db.users.find(u => u.username === username && u.password === password);
    if (user) {
      const { password: _, ...userWithoutPass } = user;
      return res.json({ success: true, user: userWithoutPass });
    }
    return res.json({ success: false, error: 'Invalid credentials' });
  }
  
  // REGISTER
  if (action === 'register') {
    const db = readDB();
    if (db.users.find(u => u.username === userData.username)) {
      return res.json({ success: false, error: 'Username already exists' });
    }
    
    const newUser = {
      id: 'user_' + Date.now(),
      username: userData.username,
      password: userData.password,
      isAdmin: false,
      displayName: userData.displayName || '@' + userData.username,
      bio: 'Selamat datang di LinkTree saya!',
      avatar: null,
      links: {
        instagram: '',
        tiktok: '',
        youtube: '',
        github: '',
        whatsapp: '',
        discord: ''
      },
      backTexts: {
        instagram: '@' + userData.username,
        tiktok: '@' + userData.username,
        youtube: userData.username,
        github: userData.username
      },
      customLinks: [],
      spotifyId: '',
      theme: 'light',
      bgImage: '',
      createdAt: new Date().toISOString()
    };
    
    db.users.push(newUser);
    writeDB(db);
    
    const { password: _, ...newUserWithoutPass } = newUser;
    return res.json({ success: true, user: newUserWithoutPass });
  }
  
  return res.json({ success: false, error: 'Unknown action' });
};
