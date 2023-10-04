$(document).ready(function() {
    var dom = document.getElementById("container");
    var myChart = echarts.init(dom);
    var app = {};
    option = null;
    option = {
        title : {
            text: heading,
            subtext: subtext,
            x:'center'
        },
        tooltip : {
            trigger: 'item',
            formatter: "{a} <br/>{b} : {c} ({d}%)"
        },
        toolbox: {
            show : false
        },
        color: ["#f62d51", "#dddddd","#ffbc34", "#7460ee","#009efb", "#2f3d4a","#90a4ae", "#55ce63"],
        calculable : true,
        series : [
            {
                name: keyText,
                type:'pie',
                radius : [20, 110],
                center : ['25%', '50%'],
                roseType : 'radius',
                label: {
                    normal: {
                        show: true
                    },
                    emphasis: {
                        show: true
                    }
                },
                lableLine: {
                    normal: {
                        show: true
                    },
                    emphasis: {
                        show: true
                    }
                },
                data: lastMonth
            },
            {
                name: keyText,
                type:'pie',
                radius : [30, 110],
                center : ['75%', '50%'],
                roseType : 'area',
                data:thisMonth
            }
        ]
    };
    ;
    if (option && typeof option === "object") {
        myChart.setOption(option, true);
    }
});

/**
 * Initialize budget slider
 */
$(".budget-slider").slider();

/**
 * When budget slider are adjusted
 */
$(".budget-slider").on("slide", function(slideEvt) {
    $(this).closest(".distribute-input").find(".allocated-budget").text(slideEvt.value);
    calculateBudget();
});

function calculateBudget(){
    var budget = parseInt($("input[name=monthly_spending]").val());
    var allocated = 0;
    $('.budget-slider').each(function(index, element){
        allocated = allocated + parseInt($(this).val());
    });
    if (allocated > budget) {
        $("#distribute").addClass("exceeded");
        $(".adjust-text").html('<span class="text-danger">You have allocated more than budgeted for a month.</span>');
    }else{
        $("#distribute").removeClass("exceeded");
        $(".adjust-text").text("Distribute your budget to categories.");
    }
}



