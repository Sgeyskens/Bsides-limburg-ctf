const hangmanStages = [
  `
    +---+
    |   |
        |
        |
        |
        |
  =========`,
  `
    +---+
    |   |
    O   |
        |
        |
        |
  =========`,
  `
    +---+
    |   |
    O   |
    |   |
        |
        |
  =========`,
  `
    +---+
    |   |
    O   |
   /|   |
        |
        |
  =========`,
  `
    +---+
    |   |
    O   |
   /|\\  |
        |
        |
  =========`,
  `
    +---+
    |   |
    O   |
   /|\\  |
   /    |
        |
  =========`,
  `
    +---+
    |   |
    O   |
   /|\\  |
   / \\  |
        |
  =========`
];

document.addEventListener('DOMContentLoaded', () => {
  const guessInput = document.getElementById('guess-input');
  const guessBtn = document.getElementById('guess-btn');
  const feedbackDiv = document.getElementById('feedback');
  const wordDisplay = document.getElementById('word-display');
  const hangmanDisplay = document.getElementById('hangman-display');
  const wrongCountDiv = document.getElementById('wrong-count');

  // Generate or retrieve user ID
  const userId = getUserId();

  // Load initial state
  loadState();

  guessBtn.addEventListener('click', makeGuess);
  guessInput.addEventListener('keypress', (e) => {
    if (e.key === 'Enter') makeGuess();
  });

  function getUserId() {
    let id = localStorage.getItem('userId');
    if (!id) {
      id = 'user_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
      localStorage.setItem('userId', id);
    }
    return id;
  }

  async function makeGuess() {
    const guess = guessInput.value.toLowerCase();
    if (!guess) return;

    try {
      const response = await fetch('/guess', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ guess, userId })
      });
      const data = await response.json();

      if (data.result === 'correct') {
        feedbackDiv.textContent = 'Correct!';
        feedbackDiv.style.color = 'green';
      } else if (data.result === 'gameOver') {
        feedbackDiv.textContent = 'Game Over! You failed. Progress reset!';
        feedbackDiv.style.color = 'red';
        guessInput.disabled = true;
        guessBtn.disabled = true;
        setTimeout(() => {
          guessInput.disabled = false;
          guessBtn.disabled = false;
          guessInput.value = '';
          loadState();
        }, 2000);
      } else {
        feedbackDiv.textContent = data.result === 'hot' ? 'Hot!' : 'Cold!';
        feedbackDiv.style.color = data.color;
      }

      loadState();
      guessInput.value = '';
    } catch (error) {
      console.error('Error:', error);
    }
  }

  async function loadState() {
    try {
      const response = await fetch(`/state?userId=${encodeURIComponent(userId)}`);
      const data = await response.json();
      wordDisplay.textContent = data.display;
      wrongCountDiv.textContent = `Wrong guesses: ${data.wrongGuesses}/6`;
      hangmanDisplay.textContent = hangmanStages[data.wrongGuesses];
      
      if (data.isComplete) {
        feedbackDiv.textContent = 'Congratulations! You guessed the word!';
        feedbackDiv.style.color = 'green';
        guessInput.disabled = true;
        guessBtn.disabled = true;
      } else if (data.gameOver) {
        feedbackDiv.textContent = 'Game Over! You failed!';
        feedbackDiv.style.color = 'red';
        guessInput.disabled = true;
        guessBtn.disabled = true;
      }
    } catch (error) {
      console.error('Error loading state:', error);
    }
  }
});