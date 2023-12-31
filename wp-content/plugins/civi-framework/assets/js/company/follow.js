var FOLLOW = FOLLOW || {};
(function ($) {
	"use strict";

	FOLLOW = {
		init: function () {
			var follow_save = civi_template_vars.follow_save,
				follow_saved = civi_template_vars.follow_saved,
				ajax_url = civi_template_vars.ajax_url;

			$("body").on("click", ".civi-add-to-follow", function (e) {
				e.preventDefault();
				if (!$(this).hasClass("on-handle")) {
					var $this = $(this).addClass("on-handle"),
						company_inner = $this
							.closest(".company-inner")
							.addClass("company-active-hover"),
						company_id = $this.attr("data-company-id"),
						save = "";

					if (!$this.hasClass("added")) {
						var offset = $this.offset(),
							width = $this.width(),
							height = $this.height(),
							coords = {
								x: offset.left + width / 2,
								y: offset.top + height / 2,
							};
					}

					$.ajax({
						type: "post",
						url: ajax_url,
						dataType: "json",
						data: {
							action: "civi_add_to_follow",
							company_id: company_id,
						},
						beforeSend: function () {
							$this.children(".icon-plus i").removeClass("far fa-plus");
							$this
								.find(".icon-plus")
								.html('<span class="civi-dual-ring"></span>');
						},
						success: function (data) {
							if (data.added) {
								$this.removeClass("removed").addClass("added");
								$this.parents(".civi-company-item").removeClass("removed-follow");
                                $this.html('<i class="fa-regular fa-heart" style="color:#2876BB; font-weight: 700;"></i>' + follow_saved);
                            } else {
								$this.removeClass("added").addClass("removed");
								$this.parents(".civi-company-item").addClass("removed-follow");
                                $this.html('<i class="fa-regular fa-heart" style="color:#2876BB;"></i>' + follow_save);
							}
							if (typeof data.added == "undefined") {
								console.log("login?");
							}
							$this.removeClass("on-handle");
							company_inner.removeClass("company-active-hover");
						},
						error: function (xhr) {
							var err = eval("(" + xhr.responseText + ")");
							$this.children("i").removeClass("fa-spinner fa-spin");
							$this.removeClass("on-handle");
							company_inner.removeClass("company-active-hover");
						},
					});
				}
			});
		},
	};
	$(document).ready(function () {
		FOLLOW.init();
	});
})(jQuery);
