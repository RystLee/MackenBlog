(function ($) {
    var Blog = {
        init: function () {
            var self = this;
            self.bootUp();
        },
        bootUp: function () {
            var self = this;
            self.initCommentList(false, false);
            self.initCommentBox();
            self.initMarkdownPreview();
            self.initTextareaAutoResize();
            self.initDeleteTarget();
            self.initHighLightCode();
        },


        initDeleteTarget: function () {
            if ($('.op-delete').length && !$('#form-delete').length) {
                $('body').append(function () {
                    return '\\n<form action="" method="POST" id="form-delete">' +
                        '\\n' + "{{ csrf_field() }}{{ method_field('delete') }}" +
                        '\\n</form>';
                });
            }

            $(document).on('click', '.op-delete', function (event) {
                event.preventDefault();
                var action = $(this).attr('data-target');
                swal({
                    title: "",
                    text: '确认删除?',
                    type: "info",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "确定",
                    cancelButtonText: "取消",
                    closeOnConfirm: false,
                    showLoaderOnConfirm: true
                }, function () {
                    $('#form-delete').attr('action', action).submit();
                });
            })
        },

        initCommentList: function (shouldMoveEnd, force) {
            var self = this;
            var container = $('#comments-container');
            if (container && force || container.children().length <= 0) {
                $.ajax({
                    method: 'get',
                    url: container.data('api-url'),
                }).done(function (data) {
                    container.html(data);
                    self.initDeleteTarget();
                    self._highLightCodeOfChild(container);
                    if (shouldMoveEnd) {
                        self._moveEnd($('#comment-submit'));
                    }
                    $(document).on('click', '.reply-user-btn', function () {
                        self._replyUser($(this).data('username'));
                    })
                });
            }
        },

        initCommentBox: function () {
            var self = this;
            var form = $('#comment-form');
            var submitBtn = form.find('#comment-submit');
            var commentContent = form.find('#comment-content');

            var username = form.find('input[name=username]');
            var email = form.find('input[name=email]');
            var site = form.find('input[name=site]');

            if (window.localStorage) {
                username.val(localStorage.getItem('comment_username') == undefined ? '' : localStorage.getItem('comment_username'));
                email.val(localStorage.getItem('comment_email') == undefined ? '' : localStorage.getItem('comment_email'));
                site.val(localStorage.getItem('comment_site') == undefined ? '' : localStorage.getItem('comment_site'));
            }

            form.on('submit', function () {

                var usernameValue = username.val();
                var emailValue = email.val();
                var siteValue = site.val();

                var errorAlert = $('<div id="comment-error-alert" class="alert alert-danger alert-dismissible" role="alert"> <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><ul></ul></div>');

                submitBtn.text('提交中...').addClass('disabled').prop('disabled', true);
                $.ajax({
                    method: 'post',
                    url: $(this).attr('action'),
                    headers: {
                        'X-CSRF-TOKEN': BlogConfig.csrfToken
                    },
                    data: {
                        commentable_id: form.find('input[name=commentable_id]').val(),
                        commentable_type: form.find('input[name=commentable_type]').val(),
                        content: commentContent.val(),
                        username: usernameValue,
                        email: emailValue,
                        site: siteValue,
                    },
                }).done(function (data) {
                    if (data.status === 200) {
                        if (window.localStorage) {
                            localStorage.setItem('comment_username', usernameValue);
                            localStorage.setItem('comment_email', emailValue);
                            localStorage.setItem('comment_site', siteValue);
                        }
                        username.val('');
                        email.val('');
                        site.val('');
                        commentContent.val('');
                        errorAlert.find('ul').html('');
                        self.initCommentList(true, true);
                    } else {
                        errorAlert.remove();
                        var errorHtml = '<li>' + data.msg + '</li>';
                        submitBtn.before(errorAlert.find('ul').html(errorHtml));
                    }
                }).fail(function(xhr) {
                    if (xhr.status === 422) {
                        var errors = $.parseJSON(xhr.responseText);
                        var errorsHtml = '';
                        $.each(errors, function( key, value ) {
                            errorsHtml += '<li>' + value[0] + '</li>'; //showing only the first error.
                        });
                        errorAlert.find('ul').html(errorsHtml);
                        errorAlert.remove();
                        submitBtn.before(errorAlert);
                    }
                }).always(function () {
                    submitBtn.text("发表").removeClass('disabled').prop('disabled', false);
                });
                return false;
            });
        },

        initTextareaAutoResize: function () {
            autosize($('textarea'));
        },

        initMarkdownPreview: function () {
            var self = this;
            $("#comment-content").focus(function (event) {
                $("#preview-box, #preview-lable").fadeIn(1500);
            });
            $('#comment-content').keyup(function () {
                self._runPreview();
            });
        },

        _runPreview: function () {
            var replyContent = $("#comment_content");
            var oldContent = replyContent.val();

            if (oldContent) {
                window.marked(oldContent, function (err, content) {
                    $('#preview-box').html(content);
                    window.emojify.run(document.getElementById('preview-box'));
                });
            }
        },


        initHighLightCode: function () {
            $('pre code').each(function (i, block) {
                hljs.highlightBlock(block);
            });
        },

        _highLightCodeOfChild: function (parent) {
            $('pre code', parent).each(function (i, block) {
                hljs.highlightBlock(block);
            });
        },

        _replyUser: function (username) {
            var self = this;
            var commentContent = $("#comment-content");
            var oldContent = commentContent.val();
            var prefix = "@" + username + " ";
            var newContent = '';
            if (oldContent.length > 0) {
                newContent = oldContent + "\n" + prefix;
            } else {
                newContent = prefix
            }
            commentContent.focus();
            commentContent.val(newContent);
            self._moveEnd(commentContent);
        },

        _moveEnd: function (obj) {
            obj.focus();
            var len = obj.value === undefined ? 0 : obj.value.length;

            if (document.selection) {
                var sel = obj.createTextRange();
                sel.moveStart('character', len);
                sel.collapse();
                sel.select();
            } else if (typeof obj.selectionStart == 'number' && typeof obj.selectionEnd == 'number') {
                obj.selectionStart = obj.selectionEnd = len;
            }
        },
    };

    window.Blog = Blog;
})(jQuery);
$(document).ready(function () {
    Blog.init();
});
