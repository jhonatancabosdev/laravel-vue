export default {

	has(errors, attribute){
		
		if (errors.length === false) {
			return false;
		}

		return errors.hasOwnProperty(attribute);
	},

	get(errors, attribute){

		if (errors.length === 0) {
			return false;
		}

		if (errors.hasOwnProperty(attribute)) {
			return Object.entries(errors).filter((key) => {
				return key[0] == attribute;
			}).map(key => {
				return key[1][0]
			}).pop()
		}
	}
	
};
