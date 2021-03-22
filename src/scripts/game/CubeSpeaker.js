import { DEFAULT_SPEAK_LANGUAGE } from "../constants.js";


export default class CubeSpeaker{
    language = DEFAULT_SPEAK_LANGUAGE

    constructor(points){
        this.points = points
        this.voices = []
        this.speak()
    }

    async speak(){
        await this.locadVoicesList();
        var u= new SpeechSynthesisUtterance;
        u.text=this.points;
        u.voice=this.getVoice();
        u.rate=1;
        u.pitch=1;
        speechSynthesis.speak(u)
    }

    async locadVoicesList(){
        return new Promise((resolve, reject) => {
            // to work with other browsers
            speechSynthesis.onvoiceschanged=() => {
                this.voices=speechSynthesis.getVoices();
                resolve(true);
            };
            // to get voices normally
            this.voices=speechSynthesis.getVoices();
            if (this.voices.length > 0){
                resolve(true);
            }
        })
    }

    getVoice(){
        const result = this.voices.find((v) => v.lang == this.language)
        // if can not find voice to language return default voice
        return result ?? this.voices[0];
    }
}