import { Maskito } from 'https://esm.sh/@maskito/core@5.1.2';
import { maskitoNumberOptionsGenerator } from 'https://esm.sh/@maskito/kit@5.1.2';

const input = document.querySelector('#amount');
if (input) {
	const options = maskitoNumberOptionsGenerator({
		minimumFractionDigits: 2,
		maximumFractionDigits: 2,
		thousandSeparator: '',
		decimalSeparator: '.',
		min: 0,
	});

	new Maskito(input, options);
}
