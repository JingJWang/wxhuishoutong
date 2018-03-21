var editor1;
var editor2;
KindEditor.ready(function(K) {
    editor1 = K.create('textarea[name="content"]', {
        cssPath : '/maijinadmin/kindeditor/plugins/code/prettify.css',
        uploadJson : '/maijinadmin/kindeditor/upload/upload_json.php',
        fileManagerJson : '/maijinadmin/kindeditor/upload/file_manager_json.php',
        minWidth : '375px',
        width : '375px',
        height : '600px',
        resizeType : 1,
        items : ['source', '|', 'undo', 'redo', '|', 'preview', 'cut', 'copy', 'paste',
                 'plainpaste', 'wordpaste', '|', '/' , 'justifyleft', 'justifycenter', 'justifyright',
                 'justifyfull', 'insertorderedlist', 'insertunorderedlist', 'indent', 'outdent', 'subscript',
                 'superscript', 'clearhtml', 'quickformat', 'selectall', '|','/', 
                 'formatblock', 'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold',
                 'italic', 'underline', 'strikethrough', 'lineheight', 'removeformat', '|', '/' , 'image', 'multiimage',
                 'media' , 'table', 'hr', 'emoticons', 'pagebreak','link', 'unlink',],
        htmlTags : {
                    section : ['style'],
                    font : ['color', 'size', 'face', '.background-color'],
                    span : [
                            '.color', '.background-color', '.font-size', '.font-family', '.background',
                            '.font-weight', '.font-style', '.text-decoration', '.vertical-align', '.line-height'
                    ],
                    div : [
                            'align', '.border', '.margin', '.padding', '.text-align', '.color',
                            '.background-color', '.font-size', '.font-family', '.font-weight', '.background',
                            '.font-style', '.text-decoration', '.vertical-align', '.margin-left'
                    ],
                    table: [
                            'border', 'cellspacing', 'cellpadding', 'width', 'height', 'align', 'bordercolor',
                            '.padding', '.margin', '.border', 'bgcolor', '.text-align', '.color', '.background-color',
                            '.font-size', '.font-family', '.font-weight', '.font-style', '.text-decoration', '.background',
                            '.width', '.height', '.border-collapse'
                    ],
                    'td,th': [
                            'align', 'valign', 'width', 'height', 'colspan', 'rowspan', 'bgcolor',
                            '.text-align', '.color', '.background-color', '.font-size', '.font-family', '.font-weight',
                            '.font-style', '.text-decoration', '.vertical-align', '.background', '.border'
                    ],
                    a : ['href', 'target', 'name'],
                    embed : ['src', 'width', 'height', 'type', 'loop', 'autostart', 'quality', '.width', '.height', 'align', 'allowscriptaccess'],
                    img : ['src', 'width', 'height', 'border', 'alt', 'title', 'align', '.width', '.height', '.border','.box-sizing','.word-wrap','.visibility','.margin','.max-width'],
                    'p,ol,ul,li,blockquote,h1,h2,h3,h4,h5,h6' : [
                            'align', '.text-align', '.color', '.background-color', '.font-size', '.font-family', '.background',
                            '.font-weight', '.font-style', '.text-decoration', '.vertical-align', '.text-indent', '.margin-left'
                            ],
                    pre : ['class'],
                    hr : ['class', '.page-break-after'],
                    'br,tbody,tr,strong,b,sub,sup,em,i,u,s' : []
                },
        newlineTag : 'div',
        allowFileManager : false,
    });

    editor2 = K.create('textarea[name="noticeContent"]', {
        cssPath : '/maijinadmin/kindeditor/plugins/code/prettify.css',
        uploadJson : '/maijinadmin/kindeditor/upload/upload_json.php',
        fileManagerJson : '/maijinadmin/kindeditor/upload/file_manager_json.php',
        minWidth : '800px',
        width : '100%',
        height : '600px',
        resizeType : 2,
        items : ['source', '|', 'undo', 'redo', '|', 'preview', 'cut', 'copy', 'paste',
                 'plainpaste', 'wordpaste', '|' , 'justifyleft', 'justifycenter', 'justifyright',
                 'justifyfull', 'insertorderedlist', 'insertunorderedlist', 'indent', 'outdent', 'subscript',
                 'superscript', 'clearhtml', 'quickformat', 'selectall', '|','/', 
                 'formatblock', 'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold',
                 'italic', 'underline', 'strikethrough', 'lineheight', 'removeformat', '|' , 'image', 'multiimage',
                 'media' , 'table', 'hr', 'emoticons', 'pagebreak','link', 'unlink',],
        htmlTags : {
                    section : ['style'],
                    font : ['color', 'size', 'face', '.background-color'],
                    span : [
                            '.color', '.background-color', '.font-size', '.font-family', '.background',
                            '.font-weight', '.font-style', '.text-decoration', '.vertical-align', '.line-height'
                    ],
                    div : [
                            'align', '.border', '.margin', '.padding', '.text-align', '.color',
                            '.background-color', '.font-size', '.font-family', '.font-weight', '.background',
                            '.font-style', '.text-decoration', '.vertical-align', '.margin-left'
                    ],
                    table: [
                            'border', 'cellspacing', 'cellpadding', 'width', 'height', 'align', 'bordercolor',
                            '.padding', '.margin', '.border', 'bgcolor', '.text-align', '.color', '.background-color',
                            '.font-size', '.font-family', '.font-weight', '.font-style', '.text-decoration', '.background',
                            '.width', '.height', '.border-collapse'
                    ],
                    'td,th': [
                            'align', 'valign', 'width', 'height', 'colspan', 'rowspan', 'bgcolor',
                            '.text-align', '.color', '.background-color', '.font-size', '.font-family', '.font-weight',
                            '.font-style', '.text-decoration', '.vertical-align', '.background', '.border'
                    ],
                    a : ['href', 'target', 'name'],
                    embed : ['src', 'width', 'height', 'type', 'loop', 'autostart', 'quality', '.width', '.height', 'align', 'allowscriptaccess'],
                    img : ['src', 'width', 'height', 'border', 'alt', 'title', 'align', '.width', '.height', '.border','.box-sizing','.word-wrap','.visibility','.margin','.max-width'],
                    'p,ol,ul,li,blockquote,h1,h2,h3,h4,h5,h6' : [
                            'align', '.text-align', '.color', '.background-color', '.font-size', '.font-family', '.background',
                            '.font-weight', '.font-style', '.text-decoration', '.vertical-align', '.text-indent', '.margin-left'
                            ],
                    pre : ['class'],
                    hr : ['class', '.page-break-after'],
                    'br,tbody,tr,strong,b,sub,sup,em,i,u,s' : []
                },
        newlineTag : 'div',
        allowFileManager : false,
    });
})