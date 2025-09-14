document.addEventListener("DOMContentLoaded", function() {
    var video = document.getElementById("bg-video");

    if (video) {
        var options = {
            root: null,
            rootMargin: "0px",
            threshold: 0.1
        };

        var observer = new IntersectionObserver(function(entries, observer) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    var source = entry.target.querySelector("source");
                    entry.target.load();
                    entry.target.play();
                    entry.target.classList.add("loaded");
                    observer.unobserve(entry.target);
                }
            });
        }, options);

        observer.observe(video);
    }
});

async function LoginWithReplit() {
    try {
        const user = await repl.auth.login();
        logLoginDataLocally(user);
        console.log('User logged in successfully');
    } catch (error) {
        console.error('Error logging in:', error);
    }
}

function logLoginDataLocally(user) {
    const loginData = {
        username: user.username,
        role: user.role, 
        timestamp: new Date().toISOString()
    };

    // Convert loginData to a string
    const loginDataString = JSON.stringify(loginData);

    
    window.requestFileSystem = window.requestFileSystem || window.webkitRequestFileSystem;

    window.requestFileSystem(window.TEMPORARY, 5 * 1024 * 1024, function(fs) {
        fs.root.getFile('login_log.txt', { create: true }, function(fileEntry) {
            fileEntry.createWriter(function(fileWriter) {
                fileWriter.onwriteend = function() {
                    console.log('Login data written to login_log.txt');
                };

                fileWriter.onerror = function(e) {
                    console.error('Error writing to login_log.txt:', e);
                };

                const blob = new Blob([loginDataString], { type: 'text/plain' });
                fileWriter.write(blob);
            });
        });
    }, function(error) {
        console.error('Error accessing FileSystem:', error);
    });
}