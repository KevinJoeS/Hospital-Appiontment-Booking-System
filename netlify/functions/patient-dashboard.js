const { getDbConnection } = require('./lib/db');

exports.handler = async (event, context) => {
  // Simple check for simulation of auth
  // In a real app, you'd check a JWT token here
  const { userId } = event.queryStringParameters || {};

  if (!userId) {
    return { statusCode: 401, body: JSON.stringify({ message: 'Unauthorized' }) };
  }

  try {
    const db = await getDbConnection();

    // Upcoming appointments
    const [upcoming] = await db.execute(
      'SELECT COUNT(*) as count FROM appointment WHERE patient_id = ? AND status = "Confirmed"',
      [userId]
    );

    // Completed appointments
    const [completed] = await db.execute(
      'SELECT COUNT(*) as count FROM appointment WHERE patient_id = ? AND status = "Completed"',
      [userId]
    );

    // Total appointments
    const [total] = await db.execute(
      'SELECT COUNT(*) as count FROM appointment WHERE patient_id = ?',
      [userId]
    );

    // Pending appointments
    const [pending] = await db.execute(
      'SELECT COUNT(*) as count FROM appointment WHERE patient_id = ? AND status = "Pending"',
      [userId]
    );

    return {
      statusCode: 200,
      body: JSON.stringify({
        success: true,
        stats: {
          upcoming: upcoming[0].count,
          completed: completed[0].count,
          total: total[0].count,
          pending: pending[0].count
        }
      }),
    };
  } catch (error) {
    console.error('Dashboard error:', error);
    return {
      statusCode: 500,
      body: JSON.stringify({ success: false, message: 'Internal Server Error' }),
    };
  }
};
