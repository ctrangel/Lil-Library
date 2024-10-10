const express = require("express");
const cors = require("cors");
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

// ------------------ Home Route ------------------

app.get("/", (req, res) => {
  res.send("Welcome to the Book API!");
});

// ------------------ All Books Routes ------------------

// GET all books with pagination, sorting, and search
app.get("/books", async (req, res) => {
  const db = getDb();
  const {
    page = 1,
    limit = 5,
    sort = "title",
    order = "asc",
    search = "",
  } = req.query;
  const offset = (page - 1) * limit;
  const validFields = ["title", "author", "avg_rating"];
  const validOrder = order === "asc" ? "ASC" : "DESC";

  if (!validFields.includes(sort)) {
    return res.status(400).json({ error: "Invalid field to sort by" });
  }

  let searchQuery = `%${search.toLowerCase()}%`;
  let booksQuery = `
    SELECT * FROM books 
    WHERE LOWER(title) LIKE $1 OR LOWER(author) LIKE $1 
    ORDER BY ${sort} ${validOrder} 
    LIMIT $2 OFFSET $3
  `;
  let countQuery = `
    SELECT COUNT(*) FROM books 
    WHERE LOWER(title) LIKE $1 OR LOWER(author) LIKE $1
  `;

  try {
    const result = await db.query(booksQuery, [searchQuery, limit, offset]);
    const countResult = await db.query(countQuery, [searchQuery]);

    res.json({
      totalBooks: countResult.rows[0].count,
      books: result.rows,
    });
  } catch (error) {
    res.status(500).json({ error: "Failed to retrieve books" });
  }
});

// ------------------ Filters/Search Routes ------------------

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
  } catch (err) {
    res
      .status(500)
      .json({ error: "An error occurred while fetching the book" });
  }
});

// ------------------ Add Routes ------------------

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
  } catch (err) {
    res.status(500).json({ error: "Could not add the book" });
  }
});

// ------------------ Update Routes ------------------

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
  } catch (err) {
    res.status(500).json({ error: "Could not update the book" });
  }
});

// ------------------ Delete Routes ------------------

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
  } catch (err) {
    res.status(500).json({ error: "Could not delete the book" });
  }
});
