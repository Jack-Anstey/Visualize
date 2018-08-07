//$(window).ready(function()
function makePie(artistCount)
{
	//console.log("Here I am, once again, feeling lost now and then");
	var genreCount = new Array();

	//divide by 7.1 is old
	var height = window.innerHeight;

	//minus 13 is the magic number for standard padding for 1080p
	//minus 9 for 1440p
	//var width = document.body.clientWidth;
	//var width = document.body.scrollWidth;

	//it's returning the value of the scrollbar as zero
	//The subtraction is a accurate estimation of the width of the screen - this gets rid of the horizontal scroll bar
	var width = $('body').innerWidth() - ($('body').innerWidth()/91.5);
	//var width = ($(document).width() - window.innerWidth);
	//console.log(width);
	//1904

	/*for(row = 0; row < artistCount.length; row++)
	{
		for(col = 0; col < artistCount[row].length; col++)
		{
			console.log(artistCount[row][col]);
		}
	}*/
	//adds two for some reason
	//Sorts the artist count array into something that can easily be placed into a javascript pie chart
	for(var row = 0; row < artistCount.length; row++)
	{
		//minus three is the first three indexes are the name of the artists, their id, and how many times they were mentioned
		for(var col = 0; col < artistCount[row].length-3; col++)
		{
			//get the number of times the artist is mentioned
			genreLength = artistCount[row][2];
			//the name of the genre at a given index
			genreName = artistCount[row][col+3];

			/*if(col==0)
			{
				console.log("genreLength: "+genreLength+", genre name: "+genreName+", Artist name: "+artistCount[row][0]);
			}*/
			//throw it into the formatted array
			if(genreCount.length!=0)
			{
				for(var gRow = 0; gRow < genreCount.length; gRow++)
		 		{
		 			if(genreCount[gRow][0] === genreName)
		 			{
		 				genreCount[gRow][1]+= genreLength;
		 				gRow = genreCount.length*2;
		 			}
		 			else if (gRow === genreCount.length-1)
		 			{
		 				genreCount.push([]);
		 				genreCount[gRow+1][0] = genreName;
		 				genreCount[gRow+1][1] = genreLength;
		 				gRow = genreCount.length*2;
		 			}
		 		}	
		 	}
		 	//if the genrename hasn't been defined in the array and it is not empty
			else if(genreName!="")
			{
				genreCount.push([]);
				genreCount[0][0] = genreName;
				genreCount[0][1] = genreLength;
				//console.log("row is " + row + ", col is " + col);
				
			}
		}
	}

	
	//convert array to the correct format
	genreJson = array2dToJson(genreCount, "", '\n');
	//convert the array into actual JSON
	genreJson = JSON.parse(genreJson);

	//from https://stackoverflow.com/questions/459105/convert-a-multidimensional-javascript-array-to-json
	//fixes the array into a format that will work with d3Pie
	function array2dToJson(a, p, nl) 
	{
		var i, j, s = '[';
		//var i, j, s = '{"' + p + '":[';
		nl = nl || '';
		for (i = 0; i < a.length; ++i) 
		{
			s += nl + array1dToJson(a[i]);
	    	if (i < a.length - 1) 
	    	{
	    		s += ',';
			}
		}
		//s += nl +'}';
		s += nl + ']';
		return s;
	}

	function array1dToJson(a, p) 
	{
		var i, s = '{';
		for (i = 0; i < a.length; ++i) 
		{
	    	if (typeof a[i] == 'string') 
	    	{
	    		//s += '"' + a[i] + '"';
	    		s += '"label" : "' + a[i] + '"';
	    	}
	    	else 
	    	{ 
	    		// assume number type
	    		s += a[i];
	    	}
	    	if (i < a.length - 1) 
	    	{
	    		//s += ',';
	    		s += ', "value" : ';
	    	}
	  	}
	  	s += '}';
	  	if (p) 
	  	{
	    	return '{"' + p + '":' + s + '}';
	  	}
	  return s;
	}

	//this contains all the settings for the appearance of the pie chart
	var pie = new d3pie("pieChart", 
	{
		header:
		{
			//title of the top
			title:
			{
				text: "Genres of Music",
				font: "Montserrat",
				fontSize: 40,
				color: "#efefef"
				
			},
			//subtitle and style
			subtitle:
			{
				text: "Contained Within Your Playlists",
				font: "Montserrat",
				fontSize: 20,
				color: "#b7bfc7"
			},
			location: "top-center",
			titleSubtitilePadding: 8
		},

		data: 
		{
			smallSegmentGrouping:
			{
				enabled: true,
				//anything below 0.5% is grouped together
				value: 0.5,
				valueType: "percentage"
			},
			sortOrder : "value-desc",
				content: genreJson
		},

		size: 
		{
			//size of the canvas is set by the size of the screen on refresh
			canvasHeight: height,
			canvasWidth: width,
			pieInnerRadius: 0,
			pieOuterRadius: null
		},

		labels:
		{
			value:
			{
				//style
				fontSize: 20,
				font: "Montserrat",
				color: "#efefef"
			},
			mainLabel:
			{
				//style
				fontSize: 20,
				font: "Montserrat",
				color: "#efefef"
			},
			outer:
			{
				//the source of this specific version of d3pie allows me to use this
				hideWhenLessThanPercentage : 2
			},
			inner:
			{
				//pertencages appear and disappear when it is less than 2 percent
				format: "percentage",
				hideWhenLessThanPercentage: 2
			},

			percentage:
			{
				//style
				fontSize: 20,
				font: "Montserrat",
				color: "#0000000"
			}
		},

		effects:
		{
			load:
			{
				//spinning animation
				speed: 2000
			},
			pullOutSegmentOnClick:
			{
				//what happens when you click on a slice
				effect : "linear"
			},
		},
		
		tooltips: 
		{
			enabled: true,
			type: "placeholder", // caption|placeholder
			string: "{label}, seen {value} times, {percentage}%",
			//string: "{label}, {value}, {percentage}%",
			placeholderParser: null,
			//tool tip style
			styles: 
			{
				fadeInSpeed: 250,
				backgroundColor: "#000000",
				backgroundOpacity: 0.5,
				color: "#efefef",
				borderRadius: 2,
				font: "Montserrat",
				fontSize: 30,
				padding: 4
			},
		},

		misc:
		{
			canvasPaddingTop: 0,
			pieCenterOffset:
			{
				//makes the distance between the pie chart and the title text smaller
				y: -height/16,
			}
		},
	});
}
//};