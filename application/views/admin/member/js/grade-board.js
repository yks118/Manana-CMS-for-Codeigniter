/**
 * clickDefault
 * 
 * @param	{Object}	f		this.form
 */
function clickDefault (f) {
	f.grade_default.value = 't';
	f.submit();
}

/**
 * clickDelete
 * 
 * @param	{Object}	f		this.form
 */
function clickDelete (f) {
	f.action = f.action.replace('updateGradeForm','deleteGradeForm');
	f.submit();
}
