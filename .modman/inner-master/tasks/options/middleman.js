/*global module:false*/
module.exports = {
  options: {
    useBundle: true
  },
  server: {
    options: {
      command: "server",
      host: "0.0.0.0"
    }
  },
  build: {
    options: {
      command: "build"
    }
  }
};