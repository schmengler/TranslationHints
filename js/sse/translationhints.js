document.observe('dom:loaded',
    function() {
        var walker = document.createTreeWalker(
            document.body, 
            NodeFilter.SHOW_TEXT, 
            null, 
            false
        );
        var decorateTranslationHints = function(textNode) {
            var newText =  textNode.nodeValue.replace(/\[__(.*?)__\]\((.*?):(.*?)\)(\(cached\))?/g,
                '$1 <span class="translation-hint-icon">i</span>' +
                '<span class="translation-hint-popup"><b>$2:</b> $3<br/>$4</span>');
            if (newText == textNode.nodeValue) {
            	return null;
            }
            var tmpElement = document.createElement('span');
            tmpElement.innerHTML = newText;
            return tmpElement.childNodes;
        }

        var node;
        var nodesToRemove = [];

        while (node = walker.nextNode()) {
        	try {
            	var newNodes = decorateTranslationHints(node);
            	
            	if (newNodes == null) {
                    continue;
                }
            	var newNode;
                while (newNode = newNodes[0]) {
                    node.parentNode.insertBefore(newNodes[0], node);
                }
                nodesToRemove.push(node);
        	} catch (e) {
        		console.log(e); 
        	}
        }
        for (var i = 0; i < nodesToRemove.length; ++i) {
            nodesToRemove[i].parentNode.removeChild(nodesToRemove[i]);
        }

    }
);