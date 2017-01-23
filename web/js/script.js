function formataNumeros(){
	$('.real').autoNumeric("init", {
		aSep: '.',
		aDec: ',', 
		aSign: 'R$ '
	});

	$('.inteiro').autoNumeric("init", {
		aSep: '.',
		aDec: ',', 
		aSign: '',
		mDec: '0'
	});

	$('.decimal2').autoNumeric("init", {
		aSep: '.',
		aDec: ',', 
		aSign: ''
	});
}