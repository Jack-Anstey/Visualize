//code base from w3 canvas bounce example. It has been heavily modified for my purposes
//make all the instance variables
//minus 8 is for padding

//learn how to change on resize
var totalWidth = window.innerWidth-8;
var maxHeight = window.innerHeight/2;
var barNum = 50;
var changeValue = 6;

var changeTimer = new Array(barNum);
var heights = new Array(barNum);
var change = new Array(barNum);
for(var index = 0; index < barNum; index++)
{
	heights[index] = Math.random()*maxHeight;
	change[index] = changeValue;
	changeTimer[index] = 0;
}

//this is the variable that makes the bars
var bars;

//function that index calls
function start() 
{
	//xI, yI, totalWidth, maxHeight, barNum, currentHeight, color
	bars = new component(totalWidth, maxHeight, barNum, "#06bb00");

    area.start();
}

//makes the canvas and updates the screen
var area = 
{
    canvas : document.createElement("canvas"),
    start : function() 
    {
        this.canvas.width = totalWidth;
        this.canvas.height = maxHeight;
        this.context = this.canvas.getContext("2d");
        document.body.insertBefore(this.canvas, document.body.childNodes[0]);
        //30 fps :/
        this.interval = setInterval(updateGameArea, 33.33333333333333);
    },
    stop : function() 
    {
        clearInterval(this.interval);
    },
    //clear the whole screen    
    clear : function() 
    {
        this.context.clearRect(0, 0, this.canvas.width, this.canvas.height);
    }
}

//this creates the instance that renders the bars
function component(totalWidth, maxHeight, barNum, color) 
{
    this.totalWidth = totalWidth;
    this.maxHeight = maxHeight;
    this.barNum = barNum;
    this.color = color;
    //changeTimer = changeTimer+1;
    var barWidth = this.totalWidth/barNum;
    var spacing = barWidth *0.1;
    barWidth-=spacing;

    this.update = function() 
    {
        //reset every frame so that this will resize properly
        barWidth = area.canvas.width/barNum;
        spacing = barWidth *0.1;
        barWidth-=spacing;

        ctx = area.context;
        ctx.fillStyle = color;
        // console.log("canvas height: " + area.canvas.height);
        // console.log("canvas width: " + totalWidth);
        // console.log(barWidth + " " + spacing);
        for(var index = 0; index < this.barNum; index++)
		{
			//instead of changing direction every frame, it checks every 5 frames and randomly decides to switch directions
			changeTimer[index]+=1;
			if(changeTimer[index] > 5)
			{
				if(Math.random() > 0.5)
				{
					change[index]*=-1;
				}
				changeTimer[index] = 0;
			}

			heights[index] += change[index];
			
			//these statements make the bars seem more lively as the 
			if(heights[index] < 0)
			{
				heights[index] = 0;
				change[index]*=-1;
			}
			if(heights[index] > area.canvas.height)
			{
				heights[index] = area.canvas.height;
				change[index]*=-1;
			}
			//console.log(changeTimer);
			//console.log("index: " + index + " height: " + heights[index] + " changeTimer: " + changeTimer[index]);
			ctx.fillRect((barWidth+spacing)*index+0, heights[index], barWidth, area.canvas.height);
		}
    }
}

//update function that runs every 33.33 milliseconds
function updateGameArea() 
{
    area.clear();
    bars.update();
}

//when javascript detects that the window has been resized, it adjusts some variables to re-render the bars to the correct width and height
window.onresize = function()
{
    var oldHeight = maxHeight;
    totalWidth = window.innerWidth-8;
    maxHeight = window.innerHeight/2;

    for(var index = 0; index < heights.length; index++)
    {
        heights[index]-=(oldHeight-area.canvas.height);
    }

    area.canvas.width = totalWidth;
    area.canvas.height = maxHeight;

    barWidth = totalWidth/barNum;
    spacing = barWidth *0.1;
    barWidth-=spacing;
}