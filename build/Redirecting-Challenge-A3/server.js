const express = require("express");
const app = express();

const FLAG = "CTF{k1ll_f0r_m0th3r}";
const PORT = 3000;

function handleRequest(req, res) {
    const index = parseInt(req.params.index || "0", 10);

    if (index >= FLAG.length) {
        // Redirect back to the root to loop
        res.redirect("/");
        return;
    }

    const char = FLAG[index];

    res.setHeader("Content-Type", "text/html");
    res.setHeader("Refresh", `0; url=/${index + 1}`);

    res.send(`
<!DOCTYPE html>
<html>
<head>
    <title>.</title>
</head>
<body style="font-size:5rem; text-align:center; margin-top:20vh;">
    ${char}
</body>
</html>
    `);
}

// Root route
app.get("/", handleRequest);

// Indexed route
app.get("/:index", handleRequest);

app.listen(PORT, () => {
    console.log(`CTF server running on http://localhost:${PORT}`);
});