const express = require("express");
const cors = require("cors");

const app = express();
const PORT = process.env.PORT || 3000;

// Allow the frontend to talk to this server
app.use(cors());
// Allow the server to read JSON from the ESP32
app.use(express.json());

// Start with a default test bin to verify the website is connecting to Railway.
let bins = [
  {
    id: "BIN-TEST",
    location: "Railway Server Connection Test",
    fillLevel: 50,
    status: "Normal",
    lastUpdated: new Date().toISOString(),
  }
];

// Endpoint for the ESP32 to send data to
app.post("/api/update-bin", (req, res) => {
  console.log("Received data from ESP32:", req.body);
  let { id, fillLevel, status, distance, binHeight = 100 } = req.body;

  // If the ESP32 sends a raw distance, calculate the fillLevel and status on the server
  if (distance !== undefined) {
    fillLevel = ((binHeight - distance) / binHeight) * 100;
    if (fillLevel < 0) fillLevel = 0;
    if (fillLevel > 100) fillLevel = 100;
    fillLevel = Math.round(fillLevel); // Round to a clean integer

    if (fillLevel >= 90) {
      status = "Full";
    } else if (fillLevel >= 70) {
      status = "Warning";
    } else {
      status = "Normal";
    }
  }

  // Find the bin and update it, or add it if it is new
  const binIndex = bins.findIndex((b) => b.id === id);
  if (binIndex !== -1) {
    bins[binIndex].fillLevel = fillLevel;
    bins[binIndex].status = status;
    bins[binIndex].lastUpdated = new Date().toISOString();
  } else {
    bins.push({
      id: id,
      location: "New Sensor Location", // Default location for new hardware
      fillLevel: fillLevel,
      status: status,
      lastUpdated: new Date().toISOString(),
    });
  }

  console.log(`Updated ${id}: ${fillLevel}% - ${status}`);
  res.status(200).send("Bin updated successfully");
});

// Endpoint to manually edit bin details from the website
app.post("/api/edit-bin", (req, res) => {
  const { id, location } = req.body;
  const binIndex = bins.findIndex((b) => b.id === id);
  
  if (binIndex !== -1) {
    if (location) bins[binIndex].location = location;
    res.status(200).send("Bin updated successfully");
  } else {
    res.status(404).send("Bin not found");
  }
});

// Endpoint for your frontend to get the data
app.get("/api/bins", (req, res) => {
  res.json(bins);
});

app.listen(PORT, "0.0.0.0", () => {
  console.log(`Server running at http://localhost:${PORT}`);
  console.log(
    `Make sure your ESP32 points to your computer's local IP on port ${PORT}`,
  );
});
