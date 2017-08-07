/**
	@detail
    Total Dynamic Sidebar Back End

    Copyright 2012  zourbuth.com  (email : zourbuth@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */
 
jQuery(document).ready(function($) {

	$('form.doughnut-form').each(function() {
		var cur = $(this), sum, arrange;
		
		$(this).bind('keyup', function() {			
			// modify the amount value
			sum = 0; 
			$("input.doughnut-amount", this).each(function() {
				sum += parseFloat( ('0' + $(this).val()).replace(/[^0-9-\.]/g, ''), 10 );
			});
			$('input[name="amount"]', cur).val(sum);
			$('.doughnut-total span', cur).html(sum);
			
			// modify the custom value
			arrange = $("input.doughnut-amount", cur).map(function(){
				return $(this).attr("name")+'='+$(this).val();
			}).get();
			console.log(arrange);
			$("[name='custom']", cur).val(arrange);
		});
	});
});