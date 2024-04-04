{include file="_header.tpl"}

		<div class="content-body">
			<div class="container">
				<div class="row">
					<main class="col-md-12">
						<article class="post post-1">
							<header class="entry-header">
								<h1 class="entry-title">
									{$reference}
								</h1>
							</header>
							<div class="entry-content clearfix">					
							{if $search_type == 'reference'}
								<hr id="passage-hr-top" style="margin-bottom:5px !important; margin-top:5px !important;clear:both; " />
								<div class="passage-inner-ref">
									{$verses}
								</div>
								<hr style="margin-bottom:15px !important; margin-top:5px !important;clear:both; " />
							{else}
								<div class="passage-title-left">{$page_start}-{$page_end} of {$versecount} verses containing <keyword style="white-space:nowrap">{$keyword_display}</keyword></div>
								<div class="passage-title-right">
									{$pagination}
								</div>
								<hr style="margin-bottom:5px !important; margin-top:5px !important;clear:both; " />
								<div class="cross-reference"></div>
								<div class="passage-inner paragraph" style="border:none;">
									{$verses}
								</div>
								<hr style="margin-bottom:15px !important; margin-top:5px !important;clear:both; " />
								<div class="passage-title-left">1-{$page_end} of {$versecount} verses containing <keyword style="white-space:nowrap">{$keyword_display}</keyword></div>
								<div class="passage-title-right">
									{$pagination}
								</div>
							{/if}					
							</div>
						</article>

					</main>

				</div>
				{$debug}
			</div>
		</div>

{include file="_footer.tpl"}
