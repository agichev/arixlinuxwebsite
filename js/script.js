document.addEventListener('DOMContentLoaded', function() {
    var header = document.getElementById('site-header');
    if (header && header.getAttribute('data-bg')) {
        header.style.backgroundImage = 'url(' + header.getAttribute('data-bg') + ')';
    }

    document.querySelectorAll('.tabs .tab').forEach(function(tab) {
        tab.addEventListener('click', function(e) {
            e.stopPropagation();
            var targetId = this.getAttribute('data-target');
            var tabContainer = this.parentElement;
            var panelContainer = tabContainer.nextElementSibling;

            tabContainer.querySelectorAll('.tab').forEach(function(t) { t.classList.remove('active'); });
            this.classList.add('active');

            Array.from(panelContainer.children).forEach(function(panel) {
                panel.classList.remove('active');
            });

            var target = document.getElementById(targetId);
            if (target) target.classList.add('active');
        });
    });

    document.querySelectorAll('.copy-btn').forEach(function(button) {
        button.addEventListener('click', function() {
            var commandBlock = this.nextElementSibling;
            var lines = commandBlock.querySelectorAll('.command-line');
            var textToCopy = Array.from(lines).map(function(line) {
                return line.textContent.trim();
            }).join('\n');

            navigator.clipboard.writeText(textToCopy).then(function() {
                this.textContent = '[Copied]';
                this.classList.add('copied');
                setTimeout(function() {
                    this.textContent = '[Copy]';
                    this.classList.remove('copied');
                }.bind(this), 2000);
            }.bind(this));
        });
    });

    var wikiSearch = document.getElementById('wiki-search');
    if (wikiSearch) {
        wikiSearch.addEventListener('input', function() {
            var query = this.value.toLowerCase();
            document.querySelectorAll('.wiki-list li').forEach(function(item) {
                var name = item.getAttribute('data-name') || '';
                item.style.display = name.indexOf(query) !== -1 ? '' : 'none';
            });
        });
    }

    var wikiContainer = document.getElementById('wiki-list-container');
    if (wikiContainer) {
        var xhr = new XMLHttpRequest();
        xhr.open('GET', '/wiki-list.php', true);
        xhr.onload = function() {
            if (xhr.status === 200) {
                wikiContainer.innerHTML = xhr.responseText;
                var input = document.getElementById('wiki-search');
                if (input) {
                    var event = new Event('input');
                    input.dispatchEvent(event);
                }
            } else {
                wikiContainer.innerHTML = '<p style="color:#888;padding:20px;text-align:center;">Failed to load wiki list.</p>';
            }
        };
        xhr.onerror = function() {
            wikiContainer.innerHTML = '<p style="color:#888;padding:20px;text-align:center;">Failed to load wiki list.</p>';
        };
        xhr.send();
    }

    var editBtn = document.getElementById('edit-post-btn');
    if (editBtn) {
        editBtn.addEventListener('click', function(e) {
            e.preventDefault();
            var token = localStorage.getItem('edit_token_' + this.getAttribute('data-post'));
            if (token) {
                window.location.href = '/editpost.php?id=' + this.getAttribute('data-post') + '&token=' + encodeURIComponent(token);
            } else {
                alert('Edit token not found. You can only edit within 20 minutes of creating the post.');
            }
        });
    }

    var deleteBtn = document.getElementById('delete-post-btn');
    if (deleteBtn) {
        deleteBtn.addEventListener('click', function(e) {
            e.preventDefault();
            if (!confirm('Are you sure you want to delete this post?')) return;
            var token = localStorage.getItem('edit_token_' + this.getAttribute('data-post'));
            if (!token) {
                alert('Edit token not found. You can only delete within 20 minutes of creating the post.');
                return;
            }
            var xhr = new XMLHttpRequest();
            xhr.open('POST', '/deletepost.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (xhr.status === 200) {
                    var data = JSON.parse(xhr.responseText);
                    if (data.ok) {
                        localStorage.removeItem('edit_token_' + deleteBtn.getAttribute('data-post'));
                        window.location.href = data.redirect || '/forum.php';
                    } else {
                        alert(data.error || 'Delete failed.');
                    }
                } else {
                    alert('Delete failed.');
                }
            };
            xhr.send('post_id=' + encodeURIComponent(this.getAttribute('data-post')) + '&token=' + encodeURIComponent(token));
        });
    }

    var postToken = document.getElementById('post-token-data');
    if (postToken) {
        var postId = postToken.getAttribute('data-post');
        var token = postToken.getAttribute('data-token');
        if (postId && token) {
            localStorage.setItem('edit_token_' + postId, token);
        }
    }
});
