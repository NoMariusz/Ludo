export default class Utilities{
    static sleep = (ms) => {
        return new Promise(resolve => setTimeout(resolve, ms));
    }
}
