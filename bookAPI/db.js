const { Pool } = require("pg");

// local db

// const pool = new Pool({
//   user: "ctrangel", // Replace with your PostgreSQL username
//   host: "localhost",
//   database: "bookDB",
//   password: "Rangel155", // Replace with your PostgreSQL password
//   port: 5432,
// });

// supabase db

const pool = new Pool({
  user: "postgres.etyhnplamagtxvxhrkqp",
  password: "col#Page9415590",
  host: "aws-0-us-east-1.pooler.supabase.com",
  database: "postgres",
  port: 6543,
  ssl: {
    rejectUnauthorized: false,
  },
});

module.exports = { pool };




function connectToDb(callback) {
  pool.connect((err, client, release) => {
    if (err) {
      console.error("Failed to connect to the database.", err);
      return callback(err);
    }
    console.log("Connected to PostgreSQL database.");
    release(); // Release the client back to the pool
    return callback();
  });
}

function getDb() {
  return pool;
}

module.exports = { connectToDb, getDb };
