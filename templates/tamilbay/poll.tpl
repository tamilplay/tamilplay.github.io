<div class="dcont ignore-select">
<p class="gradlight polltitle">{question}</p>
{list}
[voted]<div align="center">Total votes: {votes}</div>[/voted]
[not-voted]
<br>
<button class="fbutton" type="submit" onclick="doPoll('vote', '{news-id}'); return false;" ><span>Vote</span></button>
<button class="fbutton" type="submit" onclick="doPoll('results', '{news-id}'); return false;"><span>Results</span></button>
[/not-voted]
</div>