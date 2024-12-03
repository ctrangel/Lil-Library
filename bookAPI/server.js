const express = require("express");
const cors = require("cors");
const bcrypt = require("bcrypt");
const jwt = require("jsonwebtoken");
const { connectToDb, getDb } = require("./db");

const app = express();

// Use CORS middleware
app.use(cors());
app.use(express.json()); // Allows parsing JSON bodies in requests

// Connect to PostgreSQL database
connectToDb((err) => {
  if (!err) {
    app.listen(4000, () => {
      console.log("Server is running on port 4000");
    });
  } else {
    console.error("Failed to connect to the database.");
  }
});

// Logging helper function
const logUserActivity = async (userId, action, details = null) => {
  const db = getDb();
  await db.query(
    "INSERT INTO user_activity_logging (user_id, action, details) VALUES ($1, $2, $3)",
    [userId, action, details]
  );
};

// ------------------ Home Route ------------------
app.get("/", (req, res) => {
  res.send("Welcome to the Book API!");
});

// ------------------ All Books Routes ------------------

// GET all books
app.get("/books", async (req, res) => {
  const db = getDb();
  try {
    const result = await db.query("SELECT * FROM books");
    res.json(result.rows);
  } catch (error) {
    console.error("Error fetching books:", error);
    res.status(500).json({ error: "Failed to retrieve books" });
  }
});

// GET a book by ID
app.get("/books/:id", async (req, res) => {
  const db = getDb();
  const { id } = req.params;
  try {
    const result = await db.query("SELECT * FROM books WHERE id = $1", [id]);
    if (result.rows.length > 0) {
      res.json(result.rows[0]);
    } else {
      res.status(404).json({ error: "Book not found" });
    }
  } catch (error) {
    console.error("Error fetching book:", error);
    res.status(500).json({ error: "Failed to retrieve the book" });
  }
});

// POST a new book
app.post("/books", async (req, res) => {
  const db = getDb();
  const { title, author, shelves, avg_rating } = req.body;
  try {
    const result = await db.query(
      "INSERT INTO books (title, author, shelves, avg_rating) VALUES ($1, $2, $3, $4) RETURNING *",
      [title, author, shelves, avg_rating]
    );
    res.status(201).json(result.rows[0]);
  } catch (error) {
    console.error("Error adding book:", error);
    res.status(500).json({ error: "Could not add the book" });
  }
});

// PUT to update a book by ID
app.put("/books/:id", async (req, res) => {
  const db = getDb();
  const { id } = req.params;
  const { title, author, shelves, avg_rating } = req.body;
  try {
    const result = await db.query(
      "UPDATE books SET title = $1, author = $2, shelves = $3, avg_rating = $4 WHERE id = $5 RETURNING *",
      [title, author, shelves, avg_rating, id]
    );
    if (result.rowCount > 0) {
      res.json(result.rows[0]);
    } else {
      res.status(404).json({ error: "Book not found" });
    }
  } catch (error) {
    console.error("Error updating book:", error);
    res.status(500).json({ error: "Could not update the book" });
  }
});

// DELETE a book by ID
app.delete("/books/:id", async (req, res) => {
  const db = getDb();
  const { id } = req.params;
  try {
    const result = await db.query("DELETE FROM books WHERE id = $1", [id]);
    if (result.rowCount > 0) {
      res.json({ message: "Book deleted successfully" });
    } else {
      res.status(404).json({ error: "Book not found" });
    }
  } catch (error) {
    console.error("Error deleting book:", error);
    res.status(500).json({ error: "Could not delete the book" });
  }
});

// ------------------ Users Routes ------------------

// POST login
app.post("/login", async (req, res) => {
  const { username, password } = req.body;

  try {
    const db = getDb();
    const result = await db.query("SELECT * FROM users WHERE username = $1", [
      username,
    ]);
    const user = result.rows[0];

    if (user && bcrypt.compareSync(password, user.password_hash)) {
      const token = jwt.sign({ userId: user.id }, "your_jwt_secret_key", {
        expiresIn: "1h",
      });
      await logUserActivity(user.id, "login", "User logged in successfully");
      res.json({ token });
    } else {
      if (user)
        await logUserActivity(user.id, "login_failed", "Incorrect password");
      res.status(401).json({ message: "Invalid username or password" });
    }
  } catch (error) {
    res.status(500).json({ message: "Server error" });
  }
});

// POST register
app.post("/register", async (req, res) => {
  const { username, password } = req.body;

  try {
    const db = getDb();
    const hashedPassword = bcrypt.hashSync(password, 10);
    const result = await db.query(
      "INSERT INTO users (username, password_hash, created_at) VALUES ($1, $2, NOW()) RETURNING *",
      [username, hashedPassword]
    );
    await logUserActivity(
      result.rows[0].id,
      "register",
      "User registered successfully"
    );
    res.json({ success: true, message: "User registered successfully" });
  } catch (error) {
    if (error.code === "23505") {
      res.status(400).json({ message: "Username already in use" });
    } else {
      res.status(500).json({ message: "Server error" });
    }
  }
});

// POST: Add book to user's library
app.post("/userBooks", async (req, res) => {
  const db = getDb();
  const { username, bookId } = req.body;

  try {
    const userResult = await db.query(
      "SELECT id FROM users WHERE username = $1",
      [username]
    );
    if (userResult.rows.length === 0) {
      return res.status(404).json({ error: "User not found" });
    }

    const userId = userResult.rows[0].id;
    const result = await db.query(
      "INSERT INTO user_books (user_id, book_id) VALUES ($1, $2) RETURNING *",
      [userId, bookId]
    );

    await logUserActivity(
      userId,
      "add_book",
      `Added book with ID ${bookId} to library`
    );
    res.status(201).json(result.rows[0]);
  } catch (error) {
    console.error("Error adding book to user's library:", error);
    res.status(500).json({ error: "Could not add book to library" });
  }
});

// GET: Get all books in user's library
app.get("/userBooks", async (req, res) => {
  const db = getDb();
  const { username } = req.query;

  try {
    const userResult = await db.query(
      "SELECT id FROM users WHERE username = $1",
      [username]
    );
    if (userResult.rows.length === 0) {
      return res.status(404).json({ error: "User not found" });
    }

    const userId = userResult.rows[0].id;
    const result = await db.query(
      `SELECT books.* FROM books
       JOIN user_books ON books.id = user_books.book_id
       WHERE user_books.user_id = $1`,
      [userId]
    );

    await logUserActivity(
      userId,
      "view_user_books",
      "Viewed user's library books"
    );
    res.json(result.rows);
  } catch (error) {
    console.error("Error fetching user's books:", error);
    res.status(500).json({ error: "Could not retrieve user's books" });
  }
});

// ------------------ Error Handling ------------------

// Handle invalid routes
app.use((req, res) => {
  res.status(404).json({ error: "Route not found" });
});

// Handle errors
app.use((err, req, res, next) => {
  console.error(err.stack);
  res.status(500).json({ error: "Something went wrong" });
});
