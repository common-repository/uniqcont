jQuery(function ($) {
  // custom column

  $(document).on('click', '.column-ucwp_column .ucwp-check-btn', function () {
    const postId = $(this).data('id');
    const data = {
      action: 'ucwp_check_post_by_id',
      post_id: postId,
      from: 'column',
      _wpnonce: $(this).data('nonce'),
      check: $(this).data('check'),
    };

    const column = $(`#post-${postId} .ucwp-column-content`);
    column.html(column.data('progress-text') + '…');
    $.post(ajaxurl, data, function (response) {
      column.html(response);
    });
    return false;
  });

  // metabox

  function resetMetabox () {
    const matchesSelect = $('#ucwp-matches-text-select')
    if (matchesSelect.length) {
      matchesSelect.val('all');
      matchesSelect.on('change', function () {
        const url = this.value;
        $('.ucwp-text-match').hide();
        $(`.ucwp-text-match[data-url='${url}']`).show();
      });
    }
  }

  $(document).on('click', '.ucwp-check-post-btn', function () {
    const postId = $(this).data('id');
    const data = {
      action: 'ucwp_check_post_by_id',
      post_id: postId,
      from: 'metabox',
      _wpnonce: $(this).data('nonce'),
      check: $(this).data('check'),
    };

    const metabox = $('#uniqcont .inside');
    metabox.html($('#ucwp-progress-text').val() + '…');
    $.post(ajaxurl, data, function (response) {
      metabox.html(response);
      resetMetabox()
    });
    return false;
  });

  $(document).on('click', '.ucwp-show-matches', function () {
    if ($('.ucwp-text-matches').is(':visible')) {
      $(this).text($(this).data('show'));
    } else {
      $(this).text($(this).data('hide'));
    }
    $('.ucwp-text-matches').toggle();
    return false;
  });

  resetMetabox()
});
