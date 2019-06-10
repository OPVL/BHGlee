/**
 * A base class for making where clauses
 */
export declare class Where {
    constructor();
    /**
     * Convert an Object to Lucene Query Syntax
     * @param data The object that contains query string name value pairs
     * @return      resulting querystring
     */
    static toSearchSyntax(data: any): string;
    /**
     * parses part of the query value recursively into Lucene query syntax
     * @param  key         name of the field
     * @param  value       value of the field
     * @return             part of the querystring to be returned
     */
    static parseSearchValue(key: string, value: any): string;
    static writeLuceneValue(value: any): string;
    static writeLuceneValues(values: any): string;
    /**
     * Convert an Object to Database Query Syntax
     * @param  data the object that contains query string name value pairs
     * @return  resulting querystring
     */
    static toQuerySyntax(data: any): string;
    /**
     * parses a query value recursively into a Database query
     * @param  key         name of the field
     * @param  value       value of the field
     * @param  isNot       defaults to false, the reverses the logic to be parsed
     * @return             part of the querystring to be returned
     */
    static parseQueryValue(key: string, value: any, isNot?: boolean): string;
    static writeQueryValue(value: any): string | number;
    static writeQueryValues(values: any): string;
}
