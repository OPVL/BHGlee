import { AxiosInstance, AxiosResponse } from 'axios';
import { Subject } from 'rxjs';
import { RestCredentials, StaffingAuthProvider } from './StaffingAuthProvider';
import { StaffingConfiguration } from '../types';
/**
 * Used to authenticate with Bullhorn OAuth Service and track session.
 * @param config - object used to configure you bullhorn service
 * @param [config.authUrl] URL to be used for Authentication
 * @param [config.callbackUrl] URL to return to after authentication defaults to window.location
 * @param [config.clientId] Bullhorn Client ID provided by the developer center.
 * @param [config.clientSecret] Bullhorn Client Secret provided by the developer center.
 * @param [config.apiVersion] API Version to target, defaults '*' (latest)
 * @example
 * ```
 * let conn = new Staffing({
 *      BhRestToken: '~BULLHORN_REST_TOKEN~',
 *      restUrl: '~BULLHORN_REST_ENDPOING~',
 * });
 *
 * // Tracking request timings
 * Staffing.timingCallback = (url: string, time: number) {
 *   // do some tracking
 * }
 * ```
 */
export declare class Staffing {
    private readonly options;
    static unauthorized: Subject<any>;
    private static readonly _http;
    useCookies: boolean;
    accessToken: string;
    static httpInitialized: boolean;
    static trackingCallback: Function;
    config: StaffingConfiguration;
    constructor(options?: StaffingConfiguration);
    login(provider: StaffingAuthProvider): Promise<RestCredentials>;
    isLoggedIn(): Promise<AxiosResponse>;
    /**
     * Retrieves the HttpService created to connect to the Bullhorn RestApi
     */
    static http(): AxiosInstance;
    ping(): Promise<AxiosResponse>;
}
