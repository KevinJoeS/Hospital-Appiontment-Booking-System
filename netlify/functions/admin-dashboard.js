const { getDbConnection } = require('./lib/db');

exports.handler = async (event, context) => {
  // Authentication check (simplified)
  // In a real app, you'd check a JWT token here
  
  try {
    const db = await getDbConnection();

    const [doctors] = await db.execute('SELECT COUNT(*) as count FROM doctor');
    const [patients] = await db.execute('SELECT COUNT(*) as count FROM patient');
    const [appointments] = await db.execute('SELECT COUNT(*) as count FROM appointment');

    return {
      statusCode: 200,
      body: JSON.stringify({
        success: true,
        stats: {
          doctors: doctors[0].count,
          patients: patients[0].count,
          appointments: appointments[0].count
        }
      }),
    };
  } catch (error) {
    console.error('Admin dashboard error:', error);
    return {
      statusCode: 500,
      body: JSON.stringify({ success: false, message: 'Internal Server Error' }),
    };
  }
};
