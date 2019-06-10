export declare class Can {
    private readonly obj;
    constructor(obj: any);
    have(key: string): any;
    check(thing: any): boolean;
}
export declare function can(obj: any): Can;
