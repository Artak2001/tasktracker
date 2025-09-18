module.exports = {
  proxy: "http://localhost/tasktracker/public/",
  port: 3000,
  ui: { port: 3001 },
  files: [
    "D:/XAMPP/htdocs/tasktracker/**/*.php",
    "D:/XAMPP/htdocs/tasktracker/**/*.css",
    "D:/XAMPP/htdocs/tasktracker/**/*.js"
  ],
  watch: true,
  watchOptions: {
    usePolling: true,
    interval: 500,
    binaryInterval: 500
  },
  open: false,
};
