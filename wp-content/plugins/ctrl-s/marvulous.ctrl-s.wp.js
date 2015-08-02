if(marvulous === undefined)
{
	var marvulous = {};
}
if(marvulous.ctrl_s == undefined)
{
	marvulous.ctrl_s = {};
}

marvulous.ctrl_s.wp = {
	ctrl : false,
	attachEvent : function(){
		jQuery(document).keydown(marvulous.ctrl_s.wp.save);
	},
	save : function(e){
		if((e.ctrlKey || e.metaKey) && e.which == 83)
		{
			var b = false;
			if(jQuery('input[type="submit"]#save-post').length == 1)
			{
				b = jQuery('input[type="submit"]#save-post');
			}
			else if(jQuery('input[type="submit"]#publish').length == 1)
			{
				b = jQuery('input[type="submit"]#publish');
			}
			if(b != false)
			{
				var n = e.target.nodeName.toLowerCase();
				if(n == 'textarea' || n == 'input')
				{
					b.click();
					return false;
				}
			}
		}
	}
}
jQuery(document).ready(marvulous.ctrl_s.wp.attachEvent);