<?php
function get_checked_text($source_text, $highlight)
{
  $checked_text = "";
  $words = explode(" ", $source_text);
  if ($highlight && is_array($highlight)) {
    for ($i = 0; $i < count($highlight); $i++) {
      $start = $highlight[$i][0];
      $end = $highlight[$i][1];
      if (isset($words[$start]) && isset($words[$end])) {
        $words[$start] = '<span class="ucwp-match-word">' . $words[$start];
        $words[$end] = $words[$end] . "</span>";
      }
    }
    $checked_text = join(" ", $words);
  }
  return $checked_text;
}
if ($status === "checked") {
  $result_class =
    $percent >= 25
      ? "ucwp-result-red"
      : ($percent < 15
        ? "ucwp-result-green"
        : "ucwp-result-orange");
}
?>
<input type="hidden" id="ucwp-progress-text" value="<?php esc_html_e(
  "Uniqueness check in progress",
  "uniqcont"
); ?>">
<?php if ($status === "processing"): ?>
    <div class="ucwp_for_check">
        <?php esc_html_e("Uniqueness check in progress", "uniqcont"); ?><br/>
        <span class="button ucwp-check-post-btn" data-check="0" data-nonce="<?php echo esc_attr(
          $nonce
        ); ?>" data-id="<?php echo intval($post_id); ?>"><?php esc_html_e(
  "Get results without page reload",
  "uniqcont"
); ?></span>
    </div>
    <div id="ucwp_result"></div>
<?php else: ?>
    <span class="button ucwp-check-post-btn" data-check="1" data-nonce="<?php echo esc_attr(
      $nonce
    ); ?>" data-id="<?php echo intval($post_id); ?>"><?php esc_html_e(
  "Check text",
  "uniqcont"
); ?></span>
    <?php if ($status === "checked"): ?>
        <div class="ucwp_column_value">
            <p class="ucwp_result"> <?php esc_html_e(
              "Uniqueness",
              "uniqcont"
            ); ?>: <span class="<?php echo esc_attr(
  $result_class
); ?> ucwp-result-value"><?php echo esc_attr(100 - $percent); ?>%</span></p>
            <?php if ($matches && isset($matches[0]["url"])): ?>
                <table class='cw_results_table'>
                    <tr>
                        <th><?php esc_html_e("Page URL", "uniqcont"); ?></th>
                        <th><?php esc_html_e("Matching", "uniqcont"); ?></th>
                    </tr>
                    <?php foreach ($matches as $match): ?>
                        <tr>
                            <td><a href="<?php echo esc_attr(
                              $match["url"]
                            ); ?>" target="_blank"><?php echo esc_attr(
  urldecode($match["url"])
); ?></a></td>
                            <td><?php echo esc_attr($match["percent"]); ?>%</td>
                        </tr>
                    <?php endforeach; ?>
                </table>
                <p>
                    <span class="ucwp-show-matches" data-show="<?php esc_html_e(
                      "Show matches in source text ⬇",
                      "uniqcont"
                    ); ?>" data-hide="<?php esc_html_e(
  "Hide matches in source text ⬆",
  "uniqcont"
); ?>"><?php esc_html_e("Show matches in source text ⬇", "uniqcont"); ?></span>
                </p>
                <div class="ucwp-text-matches">
                  <select id="ucwp-matches-text-select">
                    <option selected value="all">All</option>
                    <?php foreach ($matches as $match): ?>
                    <option value="<?php echo esc_attr($match["url"]); ?>">
                      <span>
                        <?php
                          $option_text = '';
                          $decoded_url = esc_attr(urldecode($match["url"]));
                          $url_length = strlen($decoded_url);
                          $option_text .= $url_length > 60
                            ? substr($decoded_url, 0, 59) . "…"
                            : $decoded_url;
                          $option_text .= '&nbsp;&nbsp;&nbsp;(' . esc_attr($match["percent"]) . '%)';
                          echo $option_text;
                        ?>
                      </span>
                    </option>
                    <?php endforeach; ?>
                  </select>
                  <br />
                  <br />
                  <div id="ucwp-matches-text-all" class="ucwp-text-match" data-url="all">
                    <?php
                    $checked_text = get_checked_text($text, $highlight);
                    echo wp_kses_post($checked_text);
                    ?>
                  </div>
                  <?php foreach ($matches as $match): ?>
                    <div class="ucwp-text-match" data-url="<?php echo esc_attr(
                      $match["url"]
                    ); ?>">
                      <?php
                      $checked_text = get_checked_text(
                        $text,
                        $match["highlight"]
                      );
                      echo wp_kses_post($checked_text);
                      ?>
                    </div>
                  <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    <?php elseif ($status === "error"): ?>
        <div class="ucwp_column_value">
            <p class="ucwp_result"> <?php esc_html_e(
              "Check error",
              "uniqcont"
            ); ?>: <?php echo esc_attr($error_msg); ?></p>
        </div>
    <?php endif; ?>
<?php endif; ?>
