const mysql = require('mysql2/promise');
require('dotenv').config();

let connection;

const getDbConnection = async () => {
  if (connection) return connection;

  try {
    connection = await mysql.createConnection({
      host: process.env.DB_HOST || 'localhost',
      user: process.env.DB_USER || 'root',
      password: process.env.DB_PASSWORD || '',
      database: process.env.DB_NAME || 'hospital_db',
      port: process.env.DB_PORT || 3307,
    });
    return connection;
  } catch (error) {
    console.error('Database connection failed:', error);
    throw error;
  }
};

module.exports = { getDbConnection };
