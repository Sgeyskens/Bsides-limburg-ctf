const express = require('express');
const app = express();
const port = 3000;

// The word to guess
const targetWord = 'ctf{jason_vs_freddy}';
const userStates = new Map(); // Map of userId to { guessedPositions: Set, wrongGuesses: number }

app.use(express.json());
app.use(express.static('public'));

// Helper function to get or create user state
function getUserState(userId) {
  if (!userStates.has(userId)) {
    userStates.set(userId, { guessedPositions: new Set(), wrongGuesses: 0 });
  }
  return userStates.get(userId);
}

// API endpoint for guessing
app.post('/guess', (req, res) => {
  const { guess, userId } = req.body;
  if (!guess || guess.length !== 1) {
    return res.status(400).json({ error: 'Please provide a single character guess.' });
  }
  if (!userId) {
    return res.status(400).json({ error: 'User ID is required.' });
  }

  const userState = getUserState(userId);
  const positions = [];

  for (let i = 0; i < targetWord.length; i++) {
    if (targetWord[i] === guess && !userState.guessedPositions.has(i)) {
      userState.guessedPositions.add(i);
      positions.push(i);
    }
  }

  if (positions.length > 0) {
    // Correct guess
    res.json({ result: 'correct', positions });
  } else {
    // Incorrect guess
    userState.wrongGuesses++;
    if (userState.wrongGuesses >= 6) {
      // Game over, reset progress
      userState.guessedPositions.clear();
      userState.wrongGuesses = 0;
      res.json({ result: 'gameOver', message: 'You failed the hangman! All progress is reset.' });
    } else {
      // Calculate hot/cold
      let minDistance = Infinity;
      for (let char of targetWord) {
        const distance = Math.abs(guess.charCodeAt(0) - char.charCodeAt(0));
        if (distance < minDistance) minDistance = distance;
      }
      const isHot = minDistance < 10; // Arbitrary threshold for hot/cold
      const feedback = { result: isHot ? 'hot' : 'cold', color: isHot ? 'red' : 'blue' };
      res.json(feedback);
    }
  }
});

// API to get current state
app.get('/state', (req, res) => {
  const { userId } = req.query;
  if (!userId) {
    return res.status(400).json({ error: 'User ID is required.' });
  }

  const userState = getUserState(userId);
  const display = targetWord.split('').map((char, i) => userState.guessedPositions.has(i) ? char : '_').join('');
  const isComplete = display === targetWord;
  const gameOver = userState.wrongGuesses >= 6;
  res.json({ display, isComplete, wrongGuesses: userState.wrongGuesses, gameOver });
});

app.listen(port, () => {
  console.log(`Server running at http://localhost:${port}`);
});
