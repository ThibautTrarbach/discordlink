/*jshint esversion: 6,node: true,-W041: false */
const express = require('express');
const fs = require('fs');
const Discord = require("discord.js");

const client = new Discord.Client();

//var XMLHttpRequest = require("xmlhttprequest").XMLHttpRequest  ;
const request = require('request');

const token = process.argv[3];
const IPJeedom = process.argv[2];
const logLevel = process.argv[4];
const urlreponse = process.argv[5];
const ClePlugin =  process.argv[6];
const joueA = decodeURI(process.argv[7]);


/* Configuration */
const config = {
	logger: console2,
	token: token,
	listeningPort: 3466
};

var dernierStartServeur=0;

if (!token) config.logger('DiscordLink-Config: *********************TOKEN NON DEFINI*********************');

var hasOwnProperty = Object.prototype.hasOwnProperty;
function isEmpty(obj) {

    if (obj == null) return true;

    if (obj.length > 0)    return false;
    if (obj.length === 0)  return true;

    if (typeof obj !== "object") return true;

    for (var key in obj) {
        if (hasOwnProperty.call(obj, key)) return false;
    }

    return true;
}

function console2(text, level='') {
	var today = new Date();

	// 100=DEBUG
	// 200=INFO
	// 300=WARNING
	// 400=ERROR
	//1000=AUCUN

	try {
    	var niveauLevel;
		switch (level) {
		case "ERROR":
				niveauLevel=400;
				break;
		case "WARNING":
				niveauLevel=300;
				break;
		case "INFO":
				niveauLevel=200;
				break;
		case "DEBUG":
				niveauLevel=100;
				break;
		default:
				niveauLevel=400; //pour trouver ce qui n'a pas été affecté à un niveau
				break;
		}
	} catch (e) {
		console.log(arguments[0]);
	}


}

/* Routing */
const app = express();
let server = null;

function LancementCommande(commande, req)
{
	config.logger('DiscordLink:    Lancement /'+commande, "INFO");
}


/***** Stop the server *****/
app.get('/stop', (req, res) => {
	config.logger('DiscordLink: Shutting down');
	res.status(200).json({});
	server.close(() => {
		process.exit(0);
	});
});

/***** Restart server *****/
app.get('/restart', (req, res) => {
	config.logger('DiscordLink: Restart');
	res.status(200).json({});
		config.logger('DiscordLink: ******************************************************************');
		config.logger('DiscordLink: *****************************Relance forcée du Serveur*************');
		config.logger('DiscordLink: ******************************************************************');
	startServer();

});

app.get('/getinvite', (req, res) => {

    res.type('json');
    var toReturn = [];

	config.logger('DiscordLink: GetInvite');
    client.generateInvite(["ADMINISTRATOR"]).then(link => {
        toReturn.push({
            'invite': link
        });
        res.status(200).json(toReturn);
    }).catch(console.error);
});

app.get('/getchannel', (req, res) => {
    res.type('json');
    var toReturn = [];

	config.logger('DiscordLink: GetChannel');
    var chnannelsall = client.channels.cache.array();
    for (var b in chnannelsall) {
        var channel = chnannelsall[b];
        if (channel.type == "text") {
            toReturn.push({
                'id': channel.id,
                'name': channel.name,
				'guildID': channel.guild.id,
				'guildName' : channel.guild.name
            });
        }
    }
    res.status(200).json(toReturn);
});

app.get('/sendMsg', (req, res) => {
    res.type('json');
    var toReturn = [];

	config.logger('DiscordLink: sendMsg');
	
	toReturn.push({
        'id': req.query
    });
    res.status(200).json(toReturn);

    client.channels.cache.get(req.query.channelID).send(req.query.message);
});

app.get('/sendFile', (req, res) => {
    res.type('json');
    var toReturn = [];

    config.logger('DiscordLink: sendMsg');

    client.channels.cache.get(req.query.channelID).send(req.query.message, {
		files: [{
		  attachment: req.query.patch,
		  name: req.query.name
		}]
	});

    toReturn.push({
        'id': req.query
    });
    res.status(200).json(toReturn);
});

app.get('/sendMsgTTS', (req, res) => {
    res.type('json');
    var toReturn = [];

    config.logger('DiscordLink: sendMsgTTS');

    client.channels.cache.get(req.query.channelID).send(req.query.message, {
        tts: true
       });

    toReturn.push({
        'id': req.query
    });
    res.status(200).json(toReturn);
});

app.get('/sendEmbed', (req, res) => {
    res.type('json');
    var toReturn = [];

	config.logger('DiscordLink: sendEmbed');

	var color = req.query.color;
	var title = req.query.title;
	var url = req.query.url;
	var description = req.query.description;
	var countanswer = req.query.countanswer;
	var fields = req.query.field;
	var footer = req.query.footer;
	var reponse = "null";

	if (color =="null")color = "#ff0000";

	const Embed = new Discord.MessageEmbed()
	.setColor(color)
	.setTimestamp();
	if(title != "null") Embed.setTitle(title);
	if(url != "null" && countanswer == "null") Embed.setURL(url);
	if(description != "null") Embed.setDescription(description);
	if(footer != "null") Embed.setFooter(footer);

	if (fields != "null") {
		fields = JSON.parse(fields);
		for(var field in fields){
			var name = fields[field]['name']
			var value = fields[field]['value']
			var inline = fields[field]['inline']

			if (inline = 1) {
				inline = true
			} else {
				inline = false
			}


			console.log(fields[field])
			console.log("Name : "+name+" | Value : "+value)

			Embed.addField(name, value, inline)
		}
	}
    client.channels.cache.get(req.query.channelID).send(Embed).then(async m => {
		if(countanswer != "null") {

			toReturn.push({
				'querry': req.query,
				'timeout':req.query.timeout,
				'timecalcul': timecalcul
			});
			res.status(200).json(toReturn);

			var emojy = ["🇦","🇧","🇨","🇩","🇪","🇫","🇬","🇭","🇮","🇯","🇰","🇱","🇲","🇳","🇴","🇵","🇶","🇷","🇸","🇹","🇺","🇻","🇼","🇽","🇾","🇿"];
			a = 0;
			while (a < countanswer) {
				await m.react(emojy[a]);
				a++;
			}
			const filter = (reaction, user) => {
				return ["🇦","🇧","🇨","🇩","🇪","🇫","🇬","🇭","🇮","🇯","🇰","🇱","🇲","🇳","🇴","🇵","🇶","🇷","🇸","🇹","🇺","🇻","🇼","🇽","🇾","🇿"].includes(reaction.emoji.name) && user.id !== m.author.id;
			};

			var timecalcul = (req.query.timeout * 1000);

			m.awaitReactions(filter, { max: 1, time: timecalcul, errors: ['time'] })
			.then(collected => {
				const reaction = collected.first();
				if (reaction.emoji.name === '🇦') reponse = 0;
				else if (reaction.emoji.name === '🇧') reponse = 1;
				else if (reaction.emoji.name === '🇨') reponse = 2;
				else if (reaction.emoji.name === '🇩') reponse = 3;
				else if (reaction.emoji.name === '🇪') reponse = 4;
				else if (reaction.emoji.name === '🇫') reponse = 5;
				else if (reaction.emoji.name === '🇬') reponse = 6;
				else if (reaction.emoji.name === '🇭') reponse = 7;
				else if (reaction.emoji.name === '🇮') reponse = 8;
				else if (reaction.emoji.name === '🇯') reponse = 9;
				else if (reaction.emoji.name === '🇰') reponse = 10;
				else if (reaction.emoji.name === '🇱') reponse = 11;
				else if (reaction.emoji.name === '🇲') reponse = 12;
				else if (reaction.emoji.name === '🇳') reponse = 13;
				else if (reaction.emoji.name === '🇴') reponse = 14;
				else if (reaction.emoji.name === '🇵') reponse = 15;
				else if (reaction.emoji.name === '🇶') reponse = 16;
				else if (reaction.emoji.name === '🇷') reponse = 17;
				else if (reaction.emoji.name === '🇸') reponse = 18;
				else if (reaction.emoji.name === '🇹') reponse = 19;
				else if (reaction.emoji.name === '🇺') reponse = 20;
				else if (reaction.emoji.name === '🇻') reponse = 21;
				else if (reaction.emoji.name === '🇼') reponse = 22;
				else if (reaction.emoji.name === '🇽') reponse = 23;
				else if (reaction.emoji.name === '🇾') reponse = 24;
				else if (reaction.emoji.name === '🇿') reponse = 25;

				url = JSON.parse(url);


				httpPost("ASK",{
					idchannel: m.channel.id,
					reponse: reponse,
					demande: url
				});

			})
			.catch(collected => {
				m.delete();
			});
		}
	}).catch(console.error);
	if(countanswer == "null") {
		toReturn.push({
			'querry': req.query
		});
		res.status(200).json(toReturn);
	}
});

async function deletemessagechannel(message) {
	var date = new Date();
	var timestamp = date.getTime();
	var mindaytimestamp = timestamp-172800000;
	var maxdaytimestamp = timestamp-1206000000;
	const fetched = await message.channel.messages.fetch({force:true});
	const delmessage = new Array();

	for (const message of fetched) {
		if (maxdaytimestamp < message[1].createdTimestamp && message[1].createdTimestamp <= mindaytimestamp) {
			if (message[1].deletable) {
				delmessage.push(message[1])
			}
		}
	}
	message.channel.bulkDelete(delmessage);
};
/* Main */

startServer();

function startServer() {
    dernierStartServeur=Date.now();

    config.logger('DiscordLink:    ******************** Lancement BOT ***********************','INFO');

    client.login(config.token);

    server = app.listen(config.listeningPort, () => {
        config.logger('DiscordLink:    **************************************************************','INFO');
        config.logger('DiscordLink:    ************** Server OK listening on port ' + server.address().port + ' **************','INFO');
        config.logger('DiscordLink:    **************************************************************','INFO');

    });
}

function httpPost(nom, jsonaenvoyer) {

	var url=IPJeedom+"/plugins/discordlink/core/php/jeediscordlink.php?apikey="+ClePlugin+"&nom="+nom;

	config.logger && config.logger('URL envoyée: '+url, "DEBUG");

	jsonaenvoyer=JSON.stringify(jsonaenvoyer);
	config.logger && config.logger('DATA envoyé:'+jsonaenvoyer,'DEBUG');

	request.post(url, {
		json : true,
		gzip : false,
		multipart: [
			{
			body: jsonaenvoyer
			}
		]
	}, function (err, response, json) {

		if (!err && response.statusCode == 200) {

		} else
		{

		}
	});
}

client.on("ready", async() => {
    client.user.setActivity(joueA);
});

client.on('message', (receivedMessage) => {

    if (receivedMessage.author == client.user) {
    	if (receivedMessage.content == "!clearmessagechannel") {
			deletemessagechannel(receivedMessage);
			receivedMessage.delete();
		}
        return;
	}
	if (receivedMessage.author.bot) {
		return;
	}
	httpPost("messagerecu",{
		idchannel: receivedMessage.channel.id,
		message: receivedMessage.content,
		iduser: receivedMessage.author.id
	});
});
