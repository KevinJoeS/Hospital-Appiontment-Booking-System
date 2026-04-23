const { getDbConnection } = require('./lib/db');

exports.handler = async (event, context) => {
  if (event.httpMethod !== 'POST') {
    return { statusCode: 405, body: 'Method Not Allowed' };
  }

  try {
    const { name, email, password, phone, gender, age, dob } = JSON.parse(event.body);
    const db = await getDbConnection();

    // Check if email already exists
    const [existing] = await db.execute('SELECT id FROM patient WHERE email = ?', [email]);
    if (existing.length > 0) {
      return {
        statusCode: 400,
        body: JSON.stringify({ success: false, message: 'Email already registered' }),
      };
    }

    // Insert new patient
    const [result] = await db.execute(
      'INSERT INTO patient (name, email, password, phone, gender, age, dob) VALUES (?, ?, ?, ?, ?, ?, ?)',
      [name, email, password, phone, gender, age, dob]
    );

    return {
      statusCode: 201,
      body: JSON.stringify({
        success: true,
        user: {
          id: result.insertId,
          name: name,
          type: 'patient'
        }
      }),
    };
  } catch (error) {
    console.error('Registration error:', error);
    return {
      statusCode: 500,
      body: JSON.stringify({ success: false, message: 'Internal Server Error' }),
    };
  }
};
