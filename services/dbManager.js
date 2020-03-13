/**
 * 
 	  @author Marvis
	  @version 1.0
 * 
 */

const createOrUpdate = function(model, newItem, where) {
	return model
	.findOne({where: where})
	.then(function (foundItem) {
			if (!foundItem) {
					return model
							.create(newItem)
							.then(function (item) { return  {item: item, created: true}; }).catch(function (err) {
								console.error(err);
							});
			}else{
				return model
					.update(newItem, {where: where})
					.then(function (item) { return {item: item, created: false} }).catch(function (err) {
						console.error(err);
					});		
			}
		}
	)
}

module.exports = { createOrUpdate };