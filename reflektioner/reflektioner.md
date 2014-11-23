OPTIMERING
----------

### 1. Komprimering

Teori: Laddningstider bör vara snabbare med kompression eftersom filerna blir mindre och det då bör gå snabbare att
överföra data från servern till klienten. Speciellt tydligt bör det bli för stora filer och på långsammare uppkopplingar.
Steve Souders (Even Faster Websites, ch 9. p 121) skriver "Besides proper configuration of HTTP caching headers, enabling
gzip compression is typically the most important technique for speeding up your web page."

Åtgärd: Eftersom jag lagt applikationen på en IIS-server så har jag installerat IIS Manager, och där kan man ställa in att
man vill att responser ska komprimeras.

Observation: Jag har använt tools.pingdom.com och ser att lridge.net/1dv449/labb2/mess.php laddar 717.4kb okomprimerat och
361.0kb komprimerat. Okomprimerat tar laddningen av sidan allt från 239ms till 2,9 sekunder på 10 tester. Komprimerat
hamnar laddningstiderna i intervallet 166ms till 1,9 sekunder.

Reflektion: Känns otillfredsställande med de stora skillnaderna i laddningstid mellan de enskilda laddningarna. Jag vet
inte hur jag kan strypa uppkopplingen för att testa med en långsammare bandbredd, så jag har inte testat det. Även om
resultatet varierar kraftigt känns det ändå som testerna, om än inte så tydligt visar en skillnad mellan att ha
komprimering påslagen och avslagen. Det hade kunnat bli tydligare om jag hade en långsammare uppkoppling.

### 2. Cachning

Teori: Eftersom cachning gör att en fil som redan laddats ner en gång inte behöver laddas ner igen förrän det anges av
angivna cachningsregler, bör detta minska laddningstiderna för en sida (annat än första sidladdningen). Steve Souders
(Even Faster Websites, ch 9. p 121) skriver "Besides proper configuration of HTTP caching headers, enabling gzip
compression is typically the most important technique for speeding up your web page."

Åtgärd: Även cachningen ställer jag in i IIS Manager under HTTP Response Headers, så att web content-filer cachas i 14
dagar.

Observation: Resultatet blev en aning snabbare laddningstid som bäst 155ms, och det höll sig oftare runt 200ms och mer
sällan över sekunden.

Reflektion: Förbättringen att fler laddningstider hamnade runt 200ms kan eventuellt bero på tiden anropen gjordes till
servern (sent på natten, till skillnad från tidigare, tidigt på natten). Tools.pingdom.com ger nu nära 100%-ig
"performance grade", men klagar på en detalj som jag inte förstår. "Serve the following static resources from a domain
that doesn't set cookies: http://lridge.net/1dv449/labb2/pic/clock.png".

### 3. Bilder
Teori: Bilder som inte används men som laddas ändå är onödiga. Bakgrunden visas med heltäckande ljusblå färg och mitt
antagande är att det är så det ska vara. Denna bakgrund döljer helt b.jpg och är därför onödig. Jag ser att body-elementet
har ett background-attribut med en extern länk till http://www.lockley.net/backgds/big_leo_minor.jpg som är samma fil.
Attributet background till ett html-element stöds inte av HTML5 (http://www.w3schools.com/tags/att_body_background.asp)
och är onödigt även av den anledningen.

Åtgärd: I mess.php tar jag bort background-attributet till body-elementet och jag tar bort background-egenskapen i css:en.

Observation: Påverkade inte laddningstiden speciellt mycket. Laddningstiden var 160ms till 1.7s.

Reflektion: eftersom det var den största bildfilen så var det den bildfil som tog längst att ladda, men eftersom den ändå
inte syntes, så bör det inte ha påverkat användaren någonting alls. Det enda som egentligen påverkade var väl att servern
inte behövde belastas med 141kb i onödan. Jag minskade även logo.png och favicon.png från 41kb till 6kb, men förbättringen
borde vara minimal så jag har inte brytt mig om att mäta.

### 4. Minska förfrågningar
Teori: Tools.pingdom.com klagade helt plötsligt på för många js-anrop. Och jag har läst det innan att man ska försöka
minska antalet förfrågningar. Genom att slå ihop flera CSS-filer respektive Javascript-filer till en vardera och dessutom
minifera dem, så kan laddningstiderna ytterligare förbättras. Jag är dock osäker på om minifieringen och ihopslagningen på
serversidan sparar mer tid än vad som går åt för den extra nedladdningen.

Åtgärd: Jag letade efter olika verktyg som kunde slå ihop css- och js-filer till en, utan att det skulle krångla till
koden för mycket. Jag hittade till slut Minify (https://code.google.com/p/minify/) som jag la till. Man kunde med det
verktyget specificera länkarna till javascripten eller css-filerna man ville slå ihop så returnerar den en ihopslagen och
även minifierad javascript- eller css-fil. Jag gjorde endast detta för mess.php, och bara för javascriptfilerna (det var
bara en css-fil), men principen är densamma för båda.

Observation: Med hjälp av Minify så minskade nu den totala datamängden till 96kb, vilket är en förbättring med nästan
70kb. Laddningstiden blev 128ms som bäst, men oftast strax under en sekund. Tools.pingdom.com började nu klaga på två nya
saker. "Resources with a "?" in the URL are not cached by some proxy caching servers." vilket jag inte vet vad det betyder
eller innebär, samt att jag behöver ställa in cachning för den nämnda resursen.

Reflektion: Jag har ju ställt in cachning på klienten genom att ange header i IIS Manager, men tydligen funkar det inte på
en dynamisk resurs som denna.

Åtgärd 2: I IIS Manager la jag till en regel för Output caching för den querysträngvariabel som använd av Minify.

Observation 2: Enligt Tools.pingdom.com 126ms till strax under en sekund laddningstid, alltså inte märkbart. Men den
egentliga vinsten är ju om jag nu inte gjort detta fel att javascripten lagras mellan förfrågningarna, även nu när de
minifierats och komprimerats. Tools.pingdom.com ger nu 100% på samtliga punkter. Jag tror inte att de ihopslagna och
minifierade javascripten cachas på servern dock, så det kanske är nåt att se över för att spara lite på serverns resurser
men det får bli en annan gång.
