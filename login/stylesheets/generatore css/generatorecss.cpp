#include<iostream>
using namespace std;

int main(){
    freopen("output.txt","w",stdout);
    
	for(int i=0;i<=100;i++){
		cout<<".top"<<i<<" {top: "<<i<<"%;}"<<endl;
	}
	for(int i=0;i<=100;i++){
		cout<<".bottom"<<i<<" {bottom: "<<i<<"%;}"<<endl;
	}
	for(int i=0;i<=100;i++){
		cout<<".right"<<i<<" {right: "<<i<<"%;}"<<endl;
	}
	for(int i=0;i<=100;i++){
		cout<<".left"<<i<<" {left: "<<i<<"%;}"<<endl;
	}
	for(int i=0;i<=100;i++){
		cout<<".height"<<i<<" {height: "<<i<<"%;}"<<endl;
	}
	for(int i=0;i<=100;i++){
		cout<<".width"<<i<<" {width: "<<i<<"%;}"<<endl;
	}
}
