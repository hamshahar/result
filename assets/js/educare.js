/**
 * Educare form validation
 *
 * Autor: FixBD
 * Autor Link: https://github.com/fixbd
 * Source: https://github.com/fixbd/educare/assets/js/educare.js
 *
 */

function checkDate_Of_Birth() {
    var Date_Of_Birth = document.getElementById("Date_Of_Birth").value;
    var label = document.getElementById("Date_Of_Birth");
    
  if(Date_Of_Birth.length < 6 && Date_Of_Birth.length > 0)
    {
        label.innerHTML = "Number should be at least 6 digit long"
        return false;
    }
    
    else if(Date_Of_Birth.length > 6 && Date_Of_Birth.length > 0)
    {
        label.innerHTML = "Number should be over! 6 digit long"
        return false;
    }
    
    else
    {
        var flag = true;
        for(var i = 0; i < Date_Of_Birth.length; i++)
        {
            var code = Date_Of_Birth.charCodeAt(i);

            if (!(code > 47 && code < 58)) // numeric (0-9)
            {
                label.innerHTML = "Only numbers allowed"; 
                flag = false;
            }
            else
            {
                label.innerHTML = "";
                flag = true;
            }
        }
        return flag;

    }
}

function checkreg_no() {
    var Reg_No = document.getElementById("Reg_No").value;
    var label = document.getElementById("reg_no");
    
    if(Reg_No.length < 8 && Reg_No.length > 0)
    {
        label.innerHTML = "Number should be at least 8 digit long"
        return false;
    }
    
    else if(Reg_No.length > 8 && Reg_No.length > 0)
    {
        label.innerHTML = "Number should be over! 8 digit long"
        return false;
    }
    else {
        var flag = true;
        for(var i = 0; i < Reg_No.length; i++)
        {
            var code = Reg_No.charCodeAt(i);

            if (!(code > 47 && code < 58)) // numeric (0-9)
            {
                label.innerHTML = "Only latin numbers allowed";
                flag = false;
            }
            else
            {
                label.innerHTML = "";
                flag = true;
            }
        }
        return flag;
    }
}

function checkEmpty() {
    var fields = document.getElementsByClassName("fields");
    var labels = document.getElementsByClassName("labels");
    var flag = true;

    for(var i = 0; i<fields.length; i++)
    {
        if(fields[i].value.length < 1 || fields[i].value.length == "")
        {
            fields[i].style.backgroundColor = "red";
            labels[i].innerHTML = "Field is required";
            if(flag != false)
            flag = false;
        }
    }

    var finalFlag = flag && checkreg_no() && checkDate_Of_Birth();
    return finalFlag;

}

function resetError() {
    var labels = document.getElementsByClassName("labels");
    var fields = document.getElementsByClassName("fields");

    for(var i = 0; i < fields.length; i++)
    {
        if(this.id === fields[i].id)
        {
            labels[i].innerHTML = "";
        }
    }
    this.style.backgroundColor = "white";
}

function checkEmptyfield() {
    var myfields = document.getElementsByClassName("myfields");
    var mylabels = document.getElementsByClassName("mylabels");
    var flag = true;

    for(var i = 0; i<myfields.length; i++)
    {
        if(myfields[i].value.length < 1 || myfields[i].value.length == "")
        {
            myfields[i].style.backgroundColor = "red";
            mylabels[i].innerHTML = "Field is required";
            if(flag != false)
            flag = false;
        }
    }

    var finalFlag = flag && checkreg_no() && checkDate_Of_Birth();
    return finalFlag;

}


function resetError() {
    var mylabels = document.getElementsByClassName("mylabels");
    var myfields = document.getElementsByClassName("myfields");

    for(var i = 0; i < myfields.length; i++)
    {
        if(this.id === myfields[i].id)
        {
            mylabels[i].innerHTML = "";
        }
    }
    this.style.backgroundColor = "white";
}

//self executing below

(function(){

    var educare_results = document.getElementById("educare_results");
    educare_results.onsubmit = function(){return checkEmpty() && checkEmptyfield()};

    var Reg_No = document.getElementById("Reg_No");
    Reg_No.oninput = checkreg_no;
    Reg_No.onfocus = resetError;

    var Date_Of_Birth = document.getElementById("Date_Of_Birth");
    Date_Of_Birth.oninput = checkDate_Of_Birth;
    Date_Of_Birth.onfocus = resetError;

})();
//end Validation