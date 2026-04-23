const { getDbConnection } = require('./lib/db');

exports.handler = async (event, context) => {
  if (event.httpMethod !== 'POST') {
    return { statusCode: 405, body: 'Method Not Allowed' };
  }

  try {
    const { email, username, password, type } = JSON.parse(event.body);
    const db = await getDbConnection();

    let sql = '';
    let params = [];

    if (type === 'patient') {
      sql = 'SELECT id, name FROM patient WHERE email = ? AND password = ?';
      params = [email, password];
    } else if (type === 'admin') {
      sql = 'SELECT id, username as name FROM admin WHERE username = ? AND password = ?';
      params = [username, password];
    } else if (type === 'doctor') {
      sql = 'SELECT id, name FROM doctor WHERE email = ? AND password = ?';
      params = [email, password];
    } else {
      return { statusCode: 400, body: JSON.stringify({ message: 'Invalid user type' }) };
    }

    const [rows] = await db.execute(sql, params);

    if (rows.length === 1) {
      const user = rows[0];
      return {
        statusCode: 200,
        body: JSON.stringify({
          success: true,
          user: {
            id: user.id,
            name: user.name,
            type: type
          }
        }),
      };
    } else {
      return {
        statusCode: 401,
        body: JSON.stringify({ success: false, message: 'Invalid credentials' }),
      };
    }
  } catch (error) {
    console.error('Login error:', error);
    return {
      statusCode: 500,
      body: JSON.stringify({ success: false, message: 'Internal Server Error' }),
    };
  }
};
