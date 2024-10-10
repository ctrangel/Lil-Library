const { Pool } = require("pg");

const pool = new Pool({
  user: "ctrangel", // Replace with your PostgreSQL username
  host: "localhost",
  database: "bookDB",
  password: "Rangel155", // Replace with your PostgreSQL password
  port: 5432,
});

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
