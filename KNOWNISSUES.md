# KOBA known issues

* When refreshing resources, resources that are deleted in the AD are not removed from 
  the KOBA list. If a resource is removed in future releases, make sure it is removed 
  from the ApiKey configurations as well.
* When selecting "KOBA" as display mode for a resource, the code does not handle a mix 
  of bookings made through KOBA and Exchange (this needs further development).
