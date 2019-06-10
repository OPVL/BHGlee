import { AxiosInstance, AxiosResponse } from 'axios';
import { BullhornEntityResponse, BullhornListResponse } from '../types';
import { MetaService } from './MetaService';
/**
 * A Class that defines the base Entity Model
 * @example
 * ```
 * //Most methods on the entity are fluent (chainable)
 * var job = new Entity('JobOrder').fields('title');
 * //the 'fields' function created a getter and setter for 'title'
 * job.title = 'My New Job';
 * job.save();
 * ```
 */
export declare class EntityService<T> {
    protected endpoint: string;
    protected http: AxiosInstance;
    protected parameters: any;
    protected _fields: string[];
    type: string;
    meta: MetaService;
    /**
     * constructor
     * @param  state data to inflate object with
     */
    constructor(type: string);
    /**
     * Define the fields to set or retrieve for the given entity. Getter and Setter methods will automagically be set up on the entity once the fields are defined.
     * @param args - fields can either be sent as a list of arguments or as an Array
     */
    fields(...args: any[]): this;
    /**
     * Will merge object into the entity's parameter to be sent in any http request.
     * @param object - all additional parameters
     */
    params(object: any): this;
    /**
     * Make http request to get entity. Objects 'data' property will be set to response, then promise will be resolved.
     * @param id - Id of the Model to retrieve
     */
    get(id: number): Promise<BullhornEntityResponse<T>>;
    /**
     * Make http request to get entity with recordedit layout. Objects 'data' property will be set to response, then promise will be resolved.
     * @param id - Id of the Model to retrieve
     */
    edit(id: number): Promise<BullhornEntityResponse<T>>;
    /**
     * Make http request to get entity's full toMany relationship for a property.
     * @param property - The TO_MANY Association field
     * @param fields - Additional fields to retrieve on the TO_MANY field
     */
    many(property: string, fields: string[], value: any, params?: any): Promise<BullhornListResponse<any>>;
    /**
     * Create or Updates the entity based on the presence of an 'id' property
     */
    save(value: any): Promise<AxiosResponse>;
    /**
     * Sends a request to delete the entity
     */
    delete(id?: number): Promise<AxiosResponse>;
}
