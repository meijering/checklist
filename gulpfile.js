var gulp = require('gulp');
var mockServer = require('gulp-mock-server');

gulp.task('mock', function() {
  gulp.src('.')
    .pipe(mockServer({
      port: 9090,
    }));
});

gulp.task('start', function() {
    var connect = require('gulp-connect');
    var proxy = require('http-proxy-middleware');

    // var compression = require('compression');
    // var express = require('express');
    // var app = express();
    // app.use(compression())


//    require('connect-gzip').gzip()
    connect.server({
        root: '.',
        port: 3333,
        middleware: function() {
                return [
                    proxy([
                    '/scripts/',
                ], {
                    target: 'http://localhost:' + 9090,
                    logLevel: 'debug',
                })];
        },

    });
});

