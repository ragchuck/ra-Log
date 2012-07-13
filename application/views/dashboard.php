<? /* http://twitter.github.com/bootstrap/components.html#navs */ ?>
<div class="chart-tabs tabbable tabs-right">
      <ul class="nav nav-tabs">
            <? foreach ($chart_types as $type => $name): ?>
                  <li>
                        <?= Html::anchor("#!/chart/$type", $name, array("data-chart" => $type, "data-target" => "#tab-$type")) ?>
                  </li>
            <? endforeach ?>
      </ul>
      <div class="tab-content">
            <? foreach ($chart_types as $type => $name): ?>
                  <div class="tab-pane" id="tab-<?= $type ?>"></div>
            <? endforeach ?>
      </div>
</div>