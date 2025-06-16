// AJAX通信函数
function ajax(options) {
    const xhr = new XMLHttpRequest();
    
    options.method = options.method || 'GET';
    options.data = options.data || null;
    
    if (options.method === 'GET' && options.data) {
        options.url += '?' + new URLSearchParams(options.data).toString();
    }
    
    xhr.open(options.method, options.url, true);
    
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    options.success && options.success(response);
                } catch (e) {
                    options.error && options.error(e);
                }
            } else {
                options.error && options.error(xhr.statusText);
            }
        }
    };
    
    if (options.method === 'POST') {
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.send(new URLSearchParams(options.data).toString());
    } else {
        xhr.send();
    }
}

// 显示遮罩层
function showModal(content, buttons) {
    const modal = document.createElement('div');
    modal.className = 'modal';
    
    const modalContent = document.createElement('div');
    modalContent.className = 'modal-content';
    
    const closeBtn = document.createElement('span');
    closeBtn.className = 'close';
    closeBtn.innerHTML = '×';
    closeBtn.onclick = () => modal.remove();
    
    const contentDiv = document.createElement('div');
    contentDiv.className = 'modal-body';
    contentDiv.innerHTML = content;
    
    const buttonBar = document.createElement('div');
    buttonBar.className = 'modal-buttons';
    
    buttons.forEach(btn => {
        const button = document.createElement('button');
        button.innerHTML = btn.text;
        button.onclick = btn.onClick;
        buttonBar.appendChild(button);
    });
    
    modalContent.appendChild(closeBtn);
    modalContent.appendChild(contentDiv);
    modalContent.appendChild(buttonBar);
    modal.appendChild(modalContent);
    document.body.appendChild(modal);
} 