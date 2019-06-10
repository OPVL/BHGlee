export declare class Is {
    private readonly reference;
    constructor(ref: any);
    readonly aBoolean: boolean;
    readonly aDate: boolean;
    readonly aFunction: boolean;
    readonly anArray: boolean;
    readonly anError: boolean;
    readonly anObject: boolean;
    readonly aNonEmptyArray: boolean;
    readonly aNonEmptyString: boolean;
    readonly aNumber: boolean;
    readonly aRegExp: boolean;
    readonly aString: boolean;
    readonly defined: boolean;
    readonly nan: boolean;
    readonly null: boolean;
    readonly undefined: boolean;
    a(T: any): boolean;
    aTypeOf(type: any): boolean;
}
export declare function is(ref: any): Is;
