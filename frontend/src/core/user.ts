import { DB } from "./db";
import { History } from '.';

export class User {
    private db:DB;
    private history:History;
    
    private loginFlag = false;

    constructor (db:DB, history:History) {
        this.db = db;
        this.history = history;
    }

    public async login (email:string, pwd:string) {
        //todo:
        const res = await this.db.post(`/login`, {email, password: pwd});
        if (!res) { return false; }

        if (res.code) {
            this.loginFlag = true;
            this.history.push('/');
            return true;
        } else {
            return false;
        }
    }

    public async register (spec:{
        email:string,
        password:string,
        name:string,
    }) {
        const res = await this.db.post(`/register`, spec);
        if (!res) { return false; }

        if (res.code) {
            this.loginFlag = true;
            this.history.push('/');
            return true;
        } else {
            return false;
        }
    }

    public isAdmin () : boolean {
        // fixme:
        return true;
    }

    public isLoggedIn () : boolean {
        return this.loginFlag;
    }
}