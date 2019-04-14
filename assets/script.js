jQuery(document).ready(function ($) {

    if ($('#has_notice').length) {
        setTimeout(function () {
            $('.notice').show();
        }, 100);
    }

    /**
     * CodeMirror
     */
    if ($('#content').length) {
        var editorSettings = wp.codeEditor.defaultSettings ? _.clone(wp.codeEditor.defaultSettings) : {};
        editorSettings.codemirror = _.extend({},
            editorSettings.codemirror, {
                indentUnit: 2,
                tabSize: 2,
                mode: 'htmlmixed',
            }
        );
        var editor = wp.codeEditor.initialize($('#content'), editorSettings);
    }

    /**
     * Navigation Tabs
     */
    var tabs = $("ul.nav-tabs > li");

    for (i = 0; i < tabs.length; i++) {
        tabs[i].addEventListener("click", switchTab);
    }

    function switchTab(event) {
        event.preventDefault();

        $("ul.nav-tabs li.active").removeClass("active");
        $(".tab-pane.active").removeClass("active");

        var clickedTab = event.currentTarget;
        var anchor = event.target;
        var activePaneID = anchor.getAttribute("href");

        clickedTab.classList.add("active");
        $(activePaneID).addClass("active");

    }

    /**
     * Header Settings
     */
    var headerAjaxUrl = $('#ajaxUrl').val();
    var headerBulkDeleteAjaxUrl = $('#BulkDeleteUrl').val();

    $('#headTag #bulk-action-selector-top').on('change', function () {
        $('#hDelAll').val(this.value);
    });

    $('#headTag #bulk-action-selector-bottom').on('change', function () {
        $('#hDelAll').val(this.value);
    });

    $('#headTag #doaction').click(function () {
        if ($('#hDelAll').val() === 'delete_header') {

            var checkValues = $('.header-check:checkbox:checked').map(function () {
                return $(this).val();
            }).get();

            if (checkValues.length > 0) {
                var action = confirm("Are you sure want to delete this record(s) ?");

                if (action) {

                    $.post(headerBulkDeleteAjaxUrl, {
                            hIds: checkValues
                        },
                        function (data, status) {
                            console.log(data);
                        });
                } else {
                    return false;
                }

            }

        }
    });

    $('table.headers').on('click', '.delete-header', function () {

        var id = $(this).attr("id");

        var action = confirm("Are you sure want to delete this record ?");

        if (action) {
            $.post(headerAjaxUrl, {
                    hid: id
                },
                function (data, status) {

                    data = JSON.parse(data);

                    if (data.length != 84) {
                        var trHTML = '';

                        $.each(data, function (i, item) {
                            trHTML += item;
                        });

                        $('table.headers tbody#the-list').html(trHTML);
                    }

                });
        } else {
            return false;
        }

    });

    $('table.headers').on('click', '.header-toggle-check-input', function () {

        var id = $(this).attr("id");

        $.post(headerAjaxUrl, {
                hPosId: id
            },
            function (data, status) {

                data = JSON.parse(data);

                if (data.length != 84) {
                    var trHTML = '';

                    $.each(data, function (i, item) {
                        trHTML += item;
                    });

                    $('table.headers tbody#the-list').html(trHTML);
                }

            });

    });

    $('table.headers').on('click', '.header-ord-up', function () {

        var id = $(this).attr("id");

        $.post(headerAjaxUrl, {
                hUpId: id
            },
            function (data, status) {

                data = JSON.parse(data);

                if (data.length != 84) {
                    var trHTML = '';

                    $.each(data, function (i, item) {
                        trHTML += item;
                    });

                    $('table.headers tbody#the-list').html(trHTML);
                }


            });

    });

    $('table.headers').on('click', '.header-ord-down', function () {

        var id = $(this).attr("id");

        $.post(headerAjaxUrl, {
                hDownId: id
            },
            function (data, status) {

                data = JSON.parse(data);

                if (data.length != 84) {
                    var trHTML = '';

                    $.each(data, function (i, item) {
                        trHTML += item;
                    });

                    $('table.headers tbody#the-list').html(trHTML);
                }


            });

    });

    $('.h-position-toggle-check-input').click(function () {

        if (!$('.h-position-toggle-check-input').prop('checked')) {
            $('#h-hidden-position').val('0');
        } else {
            $('#h-hidden-position').val('1');
        }

    });

    $('.h-status-toggle-check-input').click(function () {

        if (!$('.h-status-toggle-check-input').prop('checked')) {
            $('#h-hidden-status').val('Inactive');
        } else {
            $('#h-hidden-status').val('Active');
        }

    });

    $('#headerSubmitBtn').click(function () {

        if ($('.CodeMirror-lint-marker-error')[0]) {
            $('#h-codemirror-error').val(1);
        } else {
            $('#h-codemirror-error').val(0);
        }

    });

    $('#header-all').click(function () {

        $('#header-active').removeClass('current');
        $('#header-inactive').removeClass('current');
        $(this).addClass('current');

        $.post(headerAjaxUrl, {
                hAll: true
            },
            function (data, status) {

                if (data.length > 0) {

                    data = JSON.parse(data);

                    if (data.length != 84) {
                        var trHTML = '';

                        $.each(data, function (i, item) {
                            trHTML += item;
                        });

                        $('table.headers tbody#the-list').html(trHTML);
                        $('.tablenav-header').css({
                            visibility: ''
                        });
                        $('.tablenav-header').css({
                            display: 'block'
                        });
                    }

                    console.log( data.length );

                }


            });

    });

    $('#header-active').click(function () {

        $('#header-all').removeClass('current');
        $('#header-inactive').removeClass('current');
        $(this).addClass('current');

        $.post(headerAjaxUrl, {
                hActive: true
            },
            function (data, status) {

                if (data.length > 0) {

                    data = JSON.parse(data);

                    if (data.length != 84) {

                        var trHTML = '';

                        $.each(data, function (i, item) {
                            trHTML += item;
                        });

                        $('table.headers tbody#the-list').html(trHTML);
                        $('.tablenav-header').css({
                            visibility: ''
                        });
                        $('.tablenav-header').css({
                            display: 'block'
                        });

                    }

                }


            });

    });

    $('#header-inactive').click(function () {

        $('#header-all').removeClass('current');
        $('#header-active').removeClass('current');
        $(this).addClass('current');

        $.post(headerAjaxUrl, {
                hInactive: true
            },
            function (data, status) {

                data = JSON.parse(data);

                if (data.length != 84) {

                    var trHTML = '';

                    $.each(data, function (i, item) {
                        trHTML += item;
                    });

                    $('table.headers tbody#the-list').html(trHTML);
                    $('.tablenav-header').css({
                        visibility: ''
                    });
                    $('.tablenav-header').css({
                        display: 'block'
                    });

                    return false;
                }

                $('table.headers tbody#the-list').html(data);
                $('.tablenav-header').css({
                    visibility: 'hidden'
                });
                $('.tablenav-header').css({
                    display: 'block'
                });

            });

    });

    /**
     * Body Settings
     */

    var bodyAjaxUrl = $('#ajaxUrl').val();
    var bodyBulkDeleteAjaxUrl = $('#BulkDeleteUrl').val();

    $('#bodyTag #bulk-action-selector-top').on('change', function () {
        $('#bDelAll').val(this.value);
    });

    $('#bodyTag #bulk-action-selector-bottom').on('change', function () {
        $('#bDelAll').val(this.value);
    });

    $('#bodyTag #doaction').click(function () {
        if ($('#bDelAll').val() === 'delete_body') {

            var checkValues = $('.body-check:checkbox:checked').map(function () {
                return $(this).val();
            }).get();

            if (checkValues.length > 0) {
                var action = confirm("Are you sure want to delete this record(s) ?");

                if (action) {

                    $.post(bodyBulkDeleteAjaxUrl, {
                            bIds: checkValues
                        },
                        function (data, status) {
                            console.log(data);
                        });
                } else {
                    return false;
                }

            }

        }
    });

    $('table.bodies').on('click', '.delete-body', function () {

        var id = $(this).attr("id");
        var host = window.location.hostname;
        var action = confirm("Are you sure want to delete this record ?");

        if (action) {
            $.post(bodyAjaxUrl, {
                    bid: id
                },
                function (data, status) {

                    data = JSON.parse(data);

                    if (data.length != 84) {
                        var trHTML = '';

                        $.each(data, function (i, item) {
                            trHTML += item;
                        });

                        $('table.bodies tbody#the-list').html(trHTML);
                    }


                });

        } else {
            return false;
        }

    });

    $('table.bodies').on('click', '.body-toggle-check-input', function () {

        var id = $(this).attr("id");

        $.post(bodyAjaxUrl, {
                bPosId: id
            },
            function (data, status) {

                data = JSON.parse(data);

                if (data.length != 84) {
                    var trHTML = '';

                    $.each(data, function (i, item) {
                        trHTML += item;
                    });

                    $('table.bodies tbody#the-list').html(trHTML);
                }

            });

    });

    $('table.bodies').on('click', '.body-ord-up', function () {

        var id = $(this).attr("id");

        $.post(bodyAjaxUrl, {
                bUpId: id
            },
            function (data, status) {

                data = JSON.parse(data);

                if (data.length != 84) {
                    var trHTML = '';

                    $.each(data, function (i, item) {
                        trHTML += item;
                    });

                    $('table.bodies tbody#the-list').html(trHTML);
                }

            });

    });

    $('table.bodies').on('click', '.body-ord-down', function () {

        var id = $(this).attr("id");

        $.post(bodyAjaxUrl, {
                bDownId: id
            },
            function (data, status) {

                data = JSON.parse(data);

                if (data.length != 84) {
                    var trHTML = '';

                    $.each(data, function (i, item) {
                        trHTML += item;
                    });

                    $('table.bodies tbody#the-list').html(trHTML);
                }

            });

    });

    $('.b-position-toggle-check-input').click(function () {

        if (!$('.b-position-toggle-check-input').prop('checked')) {
            $('#b-hidden-position').val('0');
        } else {
            $('#b-hidden-position').val('1');
        }

    });

    $('.b-status-toggle-check-input').click(function () {

        if (!$('.b-status-toggle-check-input').prop('checked')) {
            $('#b-hidden-status').val('Inactive');
        } else {
            $('#b-hidden-status').val('Active');
        }

    });

    $('#bodySubmitBtn').click(function () {

        if ($('.CodeMirror-lint-marker-error')[0]) {
            $('#b-codemirror-error').val(1);
        } else {
            $('#b-codemirror-error').val(0);
        }

    });

    $('#body-all').click(function () {

        $('#body-active').removeClass('current');
        $('#body-inactive').removeClass('current');
        $(this).addClass('current');

        $.post(bodyAjaxUrl, {
                bAll: true
            },
            function (data, status) {

                if (data.length > 0) {

                    data = JSON.parse(data);

                    if (data.length != 84) {
                        var trHTML = '';

                        $.each(data, function (i, item) {
                            trHTML += item;
                        });

                        $('table.bodies tbody#the-list').html(trHTML);
                        $('.tablenav-body').css({
                            visibility: ''
                        });
                        $('.tablenav-body').css({
                            display: 'block'
                        });
                    }

                }


            });

    });

    $('#body-active').click(function () {

        $('#body-all').removeClass('current');
        $('#body-inactive').removeClass('current');
        $(this).addClass('current');

        $.post(bodyAjaxUrl, {
                bActive: true
            },
            function (data, status) {

                if (data.length > 0) {

                    data = JSON.parse(data);

                    if (data.length != 84) {

                        var trHTML = '';

                        $.each(data, function (i, item) {
                            trHTML += item;
                        });

                        $('table.bodies tbody#the-list').html(trHTML);
                        $('.tablenav-body').css({
                            visibility: ''
                        });
                        $('.tablenav-body').css({
                            display: 'block'
                        });

                    }

                }


            });

    });

    $('#body-inactive').click(function () {

        $('#body-all').removeClass('current');
        $('#body-active').removeClass('current');
        $(this).addClass('current');

        $.post(bodyAjaxUrl, {
                bInactive: true
            },
            function (data, status) {

                data = JSON.parse(data);

                if (data.length != 84) {

                    var trHTML = '';

                    $.each(data, function (i, item) {
                        trHTML += item;
                    });

                    $('table.bodies tbody#the-list').html(trHTML);
                    $('.tablenav-body').css({
                        visibility: ''
                    });
                    $('.tablenav-body').css({
                        display: 'block'
                    });

                    return false;
                }

                $('table.bodies tbody#the-list').html(data);
                $('.tablenav-body').css({
                    visibility: 'hidden'
                });
                $('.tablenav-body').css({
                    display: 'block'
                });

            });

    });



});