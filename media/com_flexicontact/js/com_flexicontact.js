document.addEventListener('DOMContentLoaded', function() 
    {
    if (document.getElementById('fc_send_button'))
        document.getElementById('fc_send_button').addEventListener('submit', fc_onsubmit);
    if (document.getElementById('fc_toggle_terms'))
        document.getElementById('fc_toggle_terms').addEventListener('click', fc_show_terms);
    if (document.getElementById('fcj_agreement_check'))
        document.getElementById('fcj_agreement_check').addEventListener('click', fc_send_toggle);
    if (document.getElementById('fcj_window_open'))
        document.getElementById('fcj_window_open').addEventListener('click', fc_window_open);
    var images = document.getElementsByClassName('fc_imcap');
    for (i = 0; i < images.length; i++) 
        images[i].addEventListener('click', fc_image_select);
    }
);

function fc_onsubmit()
{
    document.getElementById('send_button').disabled = true;
}

function fc_show_terms() 
{
    var terms = document.getElementById('fc_terms');
    if (terms.className == 'fc_terms_inactive')
        terms.className = '';
	else 
        terms.className = 'fc_terms_inactive';
    return false;
}

function fc_window_open(event)
{
    event.preventDefault();
    var link = this.getAttribute('href');
    window.open(link, 'fc_terms', 'width=640,height=480,scrollbars=1,location=0,menubar=0,resizable=1');
}

function fc_send_toggle()
{
    if (this.checked)
        document.getElementById('fc_send_button').disabled = false;
    else
        document.getElementById('fc_send_button').disabled = true;
}

function fc_image_select()
{
    var images = document.getElementsByClassName('fc_imcap');
    for (var i = 0; i < images.length; i++) 
        images[i].className = 'fc_imcap fc_inactive';
    this.className = 'fc_imcap fc_active'; 
    document.getElementById('fc_picselected').value = this.id;
}
