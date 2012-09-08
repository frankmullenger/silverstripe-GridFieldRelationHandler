jQuery(function($) {
	$.entwine('ss', function($) {
		$('.ss-gridfield .ss-gridfield-item .col-noedit').entwine({
			onclick: function(e) {
				e.stopPropagation();
				e.stopImmediatePropagation();
			}
		});
		$('.ss-gridfield .ss-gridfield-item .col-noedit input').entwine({
			getState: function () {
				return this.getGridField().getState().GridFieldRelationHandler;
			},
			setState: function(val) {
				this.getGridField().setState('GridFieldRelationHandler', val);
			},
			onchange: function(e) {
				var state = this.getState();
				var input = $(e.target).closest('input');
				if(input.hasClass('radio')) {
					state.RelationVal = input.val();
				} else if(input.hasClass('checkbox')) {
					if(state.RelationVal.indexOf) {
						if(input.is(':checked')) {
							state.RelationVal.push(input.val());
						} else {
							var index = state.RelationVal.indexOf(input.val());
							if(index != -1) {
								var left = state.RelationVal.slice(0, index);
								var right = state.RelationVal.slice(index+1);
								state.RelationVal = left.concat(right);
							}
						}
					} else if(input.is(':checked')) {
						state.RelationVal = [input.val()];
					} else {
						state.RelationVal = [];
					}
				}
				this.setState(state);
			}
		});
	});
});
